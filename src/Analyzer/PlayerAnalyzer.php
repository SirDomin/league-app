<?php

namespace App\Analyzer;

use App\ApiManager\LeagueApi;
use App\Entity\Game;
use App\Entity\Participant;
use App\Provider\GameProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class PlayerAnalyzer
{
    private int $maxGames = 50;
    private FilesystemAdapter $cache;

    public function __construct(
        private LeagueApi $riot,
        private LoggerInterface $logger,
        private GameProvider $gameProvider,
    ) {
        $this->cache = new FilesystemAdapter('player_analyzer', 0);
    }

    public function analyze(string $summonerName, string $tagLine, ?int $maxGames = 50, ?string $region = 'EUN1'): array
    {
        $cacheItem = $this->cache->getItem(hash('sha256', strtolower($region . '|' . $summonerName . '#' . $tagLine . '|' . $maxGames)));

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        // 1. Get account
        $account = $this->riot->login($summonerName, $tagLine, $region);
        $puuid   = $account['puuid'];

        $this->logger->info(sprintf('Analyzing player "%s#%s" (%d games)', $summonerName, $tagLine, $maxGames));

        // 2. Get summoner info
        $summoner = $this->riot->getSummonerByPuuid($puuid, strtolower($region));
        $summoner['gameName'] = $summonerName;
        $summoner['tagLine'] = $tagLine;
        unset($summoner['puuid']);

        // 3. Ranked stats
        $ranked = $this->riot->getRankedStats($puuid, strtolower($region));

        $flexStats = $this->extractQueue($ranked, "RANKED_FLEX_SR");
        unset($flexStats['puuid']);

        $soloStats = $this->extractQueue($ranked, "RANKED_SOLO_5x5");
        unset($soloStats['puuid']);

        // 4. Match history
        $matches = $this->riot->getRankedMatchIds($puuid, $maxGames, $region);

        unset($matches['cached']);

        $matchData = [];
        foreach ($matches as $matchId) {
            if (!is_string($matchId)) {
                continue;
            }

            $this->logger->info(sprintf('Analyzing match "%s"', $matchId));
            $matchData[] = $this->gameProvider->provideGameForRegionByMatchId($matchId, $region);
        }

        // 5. Analyze champions
        $championStats = $this->analyzeChampionRolePerformance($matchData, $puuid);

        $result = [
            'summoner' => $summoner,
            'mostPlayedChampions' => $championStats['mostPlayed'],
            'rolePerformance' => $championStats['rolePerformance'],
            'rankedSoloAverage' => $soloStats,
            'rankedFlexAverage' => $flexStats
        ];

        $cacheItem->set($result);
        $cacheItem->expiresAfter(900);
        $this->cache->save($cacheItem);

        return $result;
    }

    private function extractQueue(array $ranked, string $queueType): array
    {
        unset($ranked['cached']);
        $queue = array_filter($ranked, fn($q) => $q['queueType'] === $queueType);
        return array_values($queue)[0] ?? [
            'tier' => 'UNRANKED',
            'division' => null,
            'wins' => 0,
            'losses' => 0,
        ];
    }

    private function analyzeChampionRolePerformance(array $matches, string $puuid): array
    {
        $champions = [];
        $rolePerformance = [];

        $rolePositive = [];
        $roleNegative = [];

        $roleScores = [];
        $roleCounts = [];

        $topPerRole = [];

        /** @var Game $match */
        foreach ($matches as $match) {
            if (!$match instanceof Game) {
                continue;
            }

            $info = $match->getInfo();
            $player = $this->findParticipant($match, $puuid);

            if (!$player) {
                continue;
            }

            $champId = $player->getChampionId();

            if (!isset($champions[$champId])) {
                $champions[$champId] = [
                    'games' => 0,
                    'wins' => 0,
                    'kills' => 0,
                    'deaths' => 0,
                    'assists' => 0,
                ];
            }

            $champions[$champId]['games']++;
            $champions[$champId]['wins'] += $player->getWin() ? 1 : 0;
            $champions[$champId]['kills'] += $player->getKills();
            $champions[$champId]['deaths'] += $player->getDeaths();
            $champions[$champId]['assists'] += $player->getAssists();

            $role = $player->getIndividualPosition() ?: $player->getTeamPosition() ?: 'UNKNOWN';
            $durationMinutes = max(1, $info->getGameDuration() / 60);
            $score = $this->calculateLightScore($player, $durationMinutes);
            $highlights = $this->getLightHighlights($player, $durationMinutes);

            $rolePositive[$role] ??= [];
            $roleNegative[$role] ??= [];

            $roleScores[$role] = ($roleScores[$role] ?? 0) + $score;
            $roleCounts[$role] = ($roleCounts[$role] ?? 0) + 1;

            foreach ($highlights['positive'] as $stat) {
                $rolePositive[$role][$stat] = ($rolePositive[$role][$stat] ?? 0) + 1;
            }

            foreach ($highlights['negative'] as $stat) {
                $roleNegative[$role][$stat] = ($roleNegative[$role][$stat] ?? 0) + 1;
            }

            foreach ($rolePositive as $role => $stats) {
                $topPerRole[$role]["positive"] = $this->top3($stats);
            }

            foreach ($roleNegative as $role => $stats) {
                $topPerRole[$role]["negative"] = $this->top3($stats);
            }
        }

        $roleAverages = [];

        foreach ($roleScores as $role => $totalScore) {
            $roleAverages[$role] = $totalScore / max(1, $roleCounts[$role]);
        }

        foreach ($roleAverages as $role => $avgScore) {
            $topPerRole[$role]["averageScore"] = $avgScore;
            $topPerRole[$role]["amount"] = $roleCounts[$role];
        }

        // Calculate KDA + winrate
        foreach ($champions as $id => $c) {
            $champions[$id]['winrate'] = $c['wins'] / $c['games'];
            $champions[$id]['kda'] = ($c['kills'] + $c['assists']) / max(1, $c['deaths']);
        }

        // Sort for most played
        $mostPlayed = $champions;
        uasort($mostPlayed, function ($a, $b) {
            return $b['games'] <=> $a['games'];
        });

        // Sort for best champions (winrate + KDA mix)
        $best = $champions;
        uasort($best, function ($a, $b) {
            $scoreA = ($a['winrate'] * 100) + $a['kda'];
            $scoreB = ($b['winrate'] * 100) + $b['kda'];
            return $scoreB <=> $scoreA;
        });

        // Keep top 5
        return [
            'mostPlayed' => array_slice($mostPlayed, 0, 5, true),
            'best' => array_slice($best, 0, 5, true),
            'rolePerformance' => $topPerRole,
        ];
    }

    private function top3(array $stats): array {
        arsort($stats);
        return array_slice($stats, 0, 3, true);
    }

    private function findParticipant(Game $match, string $puuid): ?Participant
    {
        /** @var Participant $participant */
        foreach ($match->getInfo()->getParticipants() as $participant) {
            if ($participant->getPuuid() === $puuid) {
                return $participant;
            }
        }

        return null;
    }

    private function calculateLightScore(Participant $player, float $durationMinutes): int
    {
        $kda = ($player->getKills() + $player->getAssists()) / max(1, $player->getDeaths());
        $csPerMinute = ($player->getTotalMinionsKilled() + $player->getNeutralMinionsKilled()) / $durationMinutes;
        $visionPerMinute = $player->getVisionScore() / $durationMinutes;
        $damagePerMinute = $player->getTotalDamageDealtToChampions() / $durationMinutes;
        $buildingDamagePerMinute = $player->getDamageDealtToBuildings() / $durationMinutes;

        $score = 50;
        $score += $player->getWin() ? 8 : -8;
        $score += min(15, $kda * 3);
        $score += min(10, $csPerMinute);
        $score += min(8, $visionPerMinute * 4);
        $score += min(10, $damagePerMinute / 80);
        $score += min(7, $buildingDamagePerMinute / 120);
        $score -= min(12, $player->getDeaths() * 1.5);

        return (int) round(max(0, min(100, $score)));
    }

    private function getLightHighlights(Participant $player, float $durationMinutes): array
    {
        $kda = ($player->getKills() + $player->getAssists()) / max(1, $player->getDeaths());
        $csPerMinute = ($player->getTotalMinionsKilled() + $player->getNeutralMinionsKilled()) / $durationMinutes;
        $visionPerMinute = $player->getVisionScore() / $durationMinutes;
        $damagePerMinute = $player->getTotalDamageDealtToChampions() / $durationMinutes;
        $buildingDamagePerMinute = $player->getDamageDealtToBuildings() / $durationMinutes;

        $positive = [];
        $negative = [];

        if ($kda >= 4) {
            $positive[] = 'high_kda';
        } elseif ($kda < 1.5) {
            $negative[] = 'low_kda';
        }

        if ($player->getDeaths() <= 3) {
            $positive[] = 'low_deaths';
        } elseif ($player->getDeaths() >= 8) {
            $negative[] = 'too_many_deaths';
        }

        if ($csPerMinute >= 7) {
            $positive[] = 'good_farm';
        } elseif ($csPerMinute < 4.5) {
            $negative[] = 'low_farm';
        }

        if ($visionPerMinute >= 1) {
            $positive[] = 'good_vision';
        } elseif ($visionPerMinute < 0.35) {
            $negative[] = 'low_vision';
        }

        if ($damagePerMinute >= 700) {
            $positive[] = 'high_damage';
        } elseif ($damagePerMinute < 350) {
            $negative[] = 'low_damage';
        }

        if ($buildingDamagePerMinute >= 250) {
            $positive[] = 'objective_pressure';
        }

        return [
            'positive' => $positive,
            'negative' => $negative,
        ];
    }

}
