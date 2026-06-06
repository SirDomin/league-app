<?php

namespace App\Serializer;

final class MatchSerializer
{
    private const DDDRAGON_ITEM_ICON     = 'https://ddragon.leagueoflegends.com/cdn/%s/img/item/%d.png';
    private const DDDRAGON_PERK_ICON     = 'https://ddragon.leagueoflegends.com/cdn/img/%s';
    private const DDDRAGON_VERSIONS_URL  = 'https://ddragon.leagueoflegends.com/api/versions.json';
    private const DDDRAGON_RUNES_URL     = 'https://ddragon.leagueoflegends.com/cdn/%s/data/en_US/runesReforged.json';

    private const DDDRAGON_SPELL_ICON = 'https://ddragon.leagueoflegends.com/cdn/%s/img/spell/%s.png';
    private const DDDRAGON_SUMMONERS_URL = 'https://ddragon.leagueoflegends.com/cdn/%s/data/en_US/summoner.json';

    private const DDDRAGON_CHAMPION_ICON = 'https://ddragon.leagueoflegends.com/cdn/%s/img/champion/%s.png';


    /** Cache w obrębie procesu (np. jednego requestu PHP-FPM) */
    private static ?string $latestDdragonVersion = null;

    /** Cache mapy run w obrębie procesu (per wersja) */
    private static array $perkIdToIconPathCache = [];

    private static array $summonerSpellIdToFileCache = [];

    /**
     * @param array<int,string> $perkIdToIconPath map: perkId => iconPath (z runesReforged.json)
     */
    public static function serialize(
        array $gameData,
        string $puuid,
        ?string $ddragonVersion = null,
        array $perkIdToIconPath = [],
    ): array {
        $ddragonVersion ??= self::getLatestDdragonVersion();
        $summonerSpellIdToFile = self::getSummonerSpellIdToFileMap($ddragonVersion);


        // ✅ jeśli nie podano mapy run, zbuduj ją automatycznie
        if (empty($perkIdToIconPath)) {
            $perkIdToIconPath = self::getPerkIdToIconPathMap($ddragonVersion);
        }

        $info = $gameData['info'] ?? [];
        $participants = $info['participants'] ?? [];

        $matchDate = null;
        if (isset($info['gameCreation'])) {
            $matchDate = (new \DateTimeImmutable())->setTimestamp((int) floor($info['gameCreation'] / 1000));
        }

        $durationSeconds = $info['gameDuration'] ?? null;

        $player = null;
        $result = null;

        $teams = [
            '100' => [],
            '200' => [],
        ];


        foreach ($participants as $p) {
            $championName = $p['championName'] ?? null;

            $teamId = (string)($p['teamId'] ?? '');
            $summoner = self::formatSummoner($p);

            $playerRow = [
                'summoner'    => $summoner,
                'champion'      => $championName,
                'championIcon'  => self::championIconSrc($championName, $ddragonVersion),
                'teamId'      => $p['teamId'] ?? null,
                'lane'        => $p['individualPosition'] ?? null,
                'summonerSpells' => self::extractSummonerSpellIcons($p, $ddragonVersion, $summonerSpellIdToFile),
                'kills'       => (int)($p['kills'] ?? 0),
                'deaths'      => (int)($p['deaths'] ?? 0),
                'assists'     => (int)($p['assists'] ?? 0),
                'kda'         => self::calcKda(
                    (int)($p['kills'] ?? 0),
                    (int)($p['deaths'] ?? 0),
                    (int)($p['assists'] ?? 0)
                ),

                'gold'        => (int)($p['goldEarned'] ?? 0),
                'cs'          => (int)($p['totalMinionsKilled'] ?? 0) + (int)($p['neutralMinionsKilled'] ?? 0),
                'visionScore' => (int)($p['visionScore'] ?? 0),

                // ✅ same src (ikony)
                'itemIcons'   => self::extractItemIconSrc($p, $ddragonVersion),
                'runes' => self::extractRunes($p, $perkIdToIconPath),
            ];

            if (!isset($teams[$teamId])) {
                $teamId = '100';
            }
            $teams[$teamId][] = $playerRow;

            if (($p['puuid'] ?? null) === $puuid) {
                $result = (($p['win'] ?? false) === true) ? 'win' : 'lose';

                $player = $playerRow;
            }
        }

        $queueId = isset($info['queueId']) ? (int)$info['queueId'] : null;
        $queueType = self::mapQueueType($queueId);

        return [
            'match' => [
                'date'             => $matchDate?->format(\DateTimeInterface::ATOM),
                'duration_seconds' => is_numeric($durationSeconds) ? (int)$durationSeconds : null,
                'queue'            => $queueType,
                'result' => $result,
            ],
            'player' => $player,
            'teams'  => [
                'blue' => $teams['100'],
                'red'  => $teams['200'],
            ],
        ];
    }

    private static function mapQueueType(?int $queueId): ?string
    {
        return match ($queueId) {
            420 => 'solo',
            440 => 'flex',
            default => null, // albo 'other'
        };
    }

    private static function getLatestDdragonVersion(): string
    {
        if (self::$latestDdragonVersion) {
            return self::$latestDdragonVersion;
        }

        $json = @file_get_contents(self::DDDRAGON_VERSIONS_URL);
        if ($json === false) {
            // lepiej rzucić wyjątek, ale zostawiam łagodny fallback
            return self::$latestDdragonVersion = 'latest';
        }

        $versions = json_decode($json, true);
        if (!is_array($versions) || !isset($versions[0]) || !is_string($versions[0])) {
            return self::$latestDdragonVersion = 'latest';
        }

        return self::$latestDdragonVersion = $versions[0];
    }

    /**
     * ✅ Pobiera runesReforged.json i buduje mapę perkId => iconPath
     * Cache w obrębie procesu per wersja.
     *
     * @return array<int,string>
     */
    private static function getPerkIdToIconPathMap(string $ddragonVersion): array
    {
        if (isset(self::$perkIdToIconPathCache[$ddragonVersion])) {
            return self::$perkIdToIconPathCache[$ddragonVersion];
        }

        $url = sprintf(self::DDDRAGON_RUNES_URL, $ddragonVersion);
        $json = @file_get_contents($url);
        if ($json === false) {
            return self::$perkIdToIconPathCache[$ddragonVersion] = [];
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            return self::$perkIdToIconPathCache[$ddragonVersion] = [];
        }

        return self::$perkIdToIconPathCache[$ddragonVersion] = self::buildPerkIconMap($data);
    }

    private static function formatSummoner(array $p): ?string
    {
        $name = $p['riotIdGameName'] ?? null;
        $tag  = $p['riotIdTagline'] ?? null;

        if (!$name || !$tag) {
            return $name ?? null;
        }

        return $name . '#' . $tag;
    }

    private static function extractItemIconSrc(array $participant, string $ddragonVersion): array
    {
        $icons = [];

        for ($i = 0; $i <= 6; $i++) {
            $itemId = (int)($participant['item' . $i] ?? 0);
            if ($itemId > 0) {
                $icons[] = sprintf(self::DDDRAGON_ITEM_ICON, $ddragonVersion, $itemId);
            }
        }

        return $icons;
    }

    /**
     * @param array<int,string> $perkIdToIconPath
     * @return array{primary: string[], secondary: string[]}
     */
    private static function extractRunes(array $participant, array $perkIdToIconPath): array
    {
        $perks = $participant['perks'] ?? [];
        $styles = $perks['styles'] ?? [];

        $result = [
            'primary' => [],
            'secondary' => [],
        ];

        if (!isset($styles[0], $styles[1])) {
            return $result;
        }

        // PRIMARY = styles[0]
        $result['primary'] = self::extractRuneStyle($styles[0], $perkIdToIconPath);

        // SECONDARY = styles[1]
        $result['secondary'] = self::extractRuneStyle($styles[1], $perkIdToIconPath);

        return $result;
    }

    /**
     * Jedna ścieżka run (primary albo secondary)
     *
     * @param array|null $perks
     * @return string[]
     */
    public static function extractMainRuneIcons(?array $perks, ?string $ddragonVersion = null): array
    {
        if ($perks === null) {
            return [];
        }

        $ddragonVersion ??= self::getLatestDdragonVersion();
        $perkIdToIconPath = self::getPerkIdToIconPathMap($ddragonVersion);
        $styles = $perks['styles'] ?? [];
        $icons = [];

        $primaryKeystoneId = $styles[0]['selections'][0]['perk'] ?? null;
        if ($primaryKeystoneId !== null && isset($perkIdToIconPath[(int) $primaryKeystoneId])) {
            $icons[] = sprintf(self::DDDRAGON_PERK_ICON, $perkIdToIconPath[(int) $primaryKeystoneId]);
        }

        $secondaryStyleId = $styles[1]['style'] ?? null;
        if ($secondaryStyleId !== null && isset($perkIdToIconPath[(int) $secondaryStyleId])) {
            $icons[] = sprintf(self::DDDRAGON_PERK_ICON, $perkIdToIconPath[(int) $secondaryStyleId]);
        }

        return $icons;
    }

    /**
     * @param array $style
     * @param array<int,string> $perkIdToIconPath
     * @return string[]
     */
    private static function extractRuneStyle(array $style, array $perkIdToIconPath): array
    {
        $icons = [];

        // ikona ścieżki (Precision/Domination/etc)
        if (isset($style['style'])) {
            $styleId = (int)$style['style'];
            if (isset($perkIdToIconPath[$styleId])) {
                $icons[] = sprintf(self::DDDRAGON_PERK_ICON, $perkIdToIconPath[$styleId]);
            }
        }

        // ikony wybranych run
        foreach (($style['selections'] ?? []) as $selection) {
            $perkId = (int)($selection['perk'] ?? 0);
            if ($perkId > 0 && isset($perkIdToIconPath[$perkId])) {
                $icons[] = sprintf(self::DDDRAGON_PERK_ICON, $perkIdToIconPath[$perkId]);
            }
        }

        return $icons;
    }


    private static function calcKda(int $kills, int $deaths, int $assists): float
    {
        $den = $deaths === 0 ? 1 : $deaths;
        return round(($kills + $assists) / $den, 2);
    }

    /**
     * ✅ Buduje mapę perkId => iconPath z runesReforged.json
     *
     * @param array $runesReforgedJson
     * @return array<int,string>
     */
    private static function buildPerkIconMap(array $runesReforgedJson): array
    {
        $map = [];

        foreach ($runesReforgedJson as $style) {
            // Ikona ścieżki (Precision/Domination itd.)
            if (isset($style['id'], $style['icon'])) {
                $map[(int)$style['id']] = (string)$style['icon'];
            }

            foreach (($style['slots'] ?? []) as $slot) {
                foreach (($slot['runes'] ?? []) as $rune) {
                    if (isset($rune['id'], $rune['icon'])) {
                        $map[(int)$rune['id']] = (string)$rune['icon'];
                    }
                }
            }
        }

        return $map;
    }

    /**
     * @return array<int,string> map: spellId => image.full (np. "SummonerFlash.png")
     */
    private static function getSummonerSpellIdToFileMap(string $ddragonVersion): array
    {
        if (isset(self::$summonerSpellIdToFileCache[$ddragonVersion])) {
            return self::$summonerSpellIdToFileCache[$ddragonVersion];
        }

        $url = sprintf(self::DDDRAGON_SUMMONERS_URL, $ddragonVersion);
        $json = @file_get_contents($url);
        if ($json === false) {
            return self::$summonerSpellIdToFileCache[$ddragonVersion] = [];
        }

        $data = json_decode($json, true);
        if (!is_array($data) || !isset($data['data']) || !is_array($data['data'])) {
            return self::$summonerSpellIdToFileCache[$ddragonVersion] = [];
        }

        $map = [];
        foreach ($data['data'] as $spell) {
            // w Data Dragon "key" to string z ID (np. "4" dla Flash)
            $id = isset($spell['key']) ? (int)$spell['key'] : 0;
            $file = $spell['image']['full'] ?? null;

            if ($id > 0 && is_string($file) && $file !== '') {
                // zwykle file już ma .png, ale my ujednolicimy później
                $map[$id] = preg_replace('/\.png$/i', '', $file) ?: $file;
            }
        }

        return self::$summonerSpellIdToFileCache[$ddragonVersion] = $map;
    }

    /**
     * Zwraca ikony dwóch summoner spellów (D/F) jako src.
     *
     * @param array<int,string> $summonerSpellIdToFile map: spellId => filename bez .png (np. "SummonerFlash")
     * @return string[] np. ["https://.../SummonerFlash.png", "https://.../SummonerTeleport.png"]
     */
    private static function extractSummonerSpellIcons(array $participant, string $ddragonVersion, array $summonerSpellIdToFile): array
    {
        $ids = [
            (int)($participant['summoner1Id'] ?? 0),
            (int)($participant['summoner2Id'] ?? 0),
        ];

        $icons = [];

        foreach ($ids as $id) {
            if ($id <= 0) {
                continue;
            }

            $file = $summonerSpellIdToFile[$id] ?? null;
            if (!$file) {
                continue;
            }

            // $file może być "SummonerFlash" albo "SummonerFlash.png" — ujednolicamy
            $file = preg_replace('/\.png$/i', '', $file) ?: $file;

            $icons[] = sprintf(self::DDDRAGON_SPELL_ICON, $ddragonVersion, $file);
        }

        return $icons;
    }

    private static function championIconSrc(?string $championName, string $ddragonVersion): ?string
    {
        if (!$championName) {
            return null;
        }

        // Data Dragon używa "data" name (np. "Wukong" zamiast "MonkeyKing") – ale match-v5 zwykle już daje właściwe.
        // Zostawiamy proste sanitize.
        $championName = preg_replace('/[^A-Za-z0-9]/', '', $championName) ?: $championName;

        return sprintf(self::DDDRAGON_CHAMPION_ICON, $ddragonVersion, $championName);
    }
}
