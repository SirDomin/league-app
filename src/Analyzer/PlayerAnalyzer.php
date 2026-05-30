<?php

namespace App\Analyzer;

use App\ApiManager\LeagueApi;
use App\Calculator\ScoreCalculator;
use App\Entity\Game;
use App\Entity\Participant;
use App\Provider\GameProvider;
use Psr\Log\LoggerInterface;

class PlayerAnalyzer
{
    private int $maxGames = 50;

    public function __construct(
        private LeagueApi $riot,
        private LoggerInterface $logger,
        private GameProvider $gameProvider,
        private ScoreCalculator $scoreCalculator,
    ) {}

    public function analyze(string $summonerName, string $tagLine, ?int $maxGames = 50, ?string $region = 'EUN1'): array
    {
        // 1. Get account
        $account = $this->riot->login($summonerName, $tagLine, $region);
        $puuid   = $account['puuid'];

        $this->logger->info(sprintf('Analyzing player "%s"', $summonerName));

        // 2. Get summoner info
        $summoner = $this->riot->getSummonerByPuuid($puuid, strtolower($region));
        $summoner['gameName'] = $summonerName;
        $summoner['tagLine'] = $tagLine;
        unset($summoner['puuid']);

        $this->logger->info(json_encode($summoner));

        // 3. Ranked stats
        $ranked = $this->riot->getRankedStats($puuid, strtolower($region));

        $this->logger->info(json_encode($ranked));

        $flexStats = $this->extractQueue($ranked, "RANKED_FLEX_SR");
        unset($flexStats['puuid']);

        $soloStats = $this->extractQueue($ranked, "RANKED_SOLO_5x5");
        unset($soloStats['puuid']);

        // 4. Match history
        $matches = $this->riot->getRankedMatchIds($puuid, $maxGames, $region);

        unset($matches['cached']);

        $matchData = [];
        foreach ($matches as $matchId) {
            $this->logger->info(sprintf('Analyzing match "%s"', $matchId));
            $matchData[] = $this->scoreCalculator->calculateScoreForGame($this->gameProvider->provideGameForRegionByMatchId($matchId, $region));
        }

        // 5. Analyze champions
        $championStats = $this->analyzeChampionRolePerformance($matchData, $puuid);

        return [
            'summoner' => $summoner,
            'mostPlayedChampions' => $championStats['mostPlayed'],
            'rolePerformance' => $championStats['rolePerformance'],
            'rankedSoloAverage' => $soloStats,
            'rankedFlexAverage' => $flexStats
        ];
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

        $globalPositive = [];
        $globalNegative = [];

        $rolePositive = [];
        $roleNegative = [];

        $roleScores = [];
        $roleCounts = [];

        $topPerRole = [];

        /** @var Game $match */
        foreach ($matches as $match) {
            if ($match !== []) {
                $this->logger->info(sprintf('Analyzing match'));
                $this->logger->info(json_encode($match));
                $info = $match->getInfo();

                // find the player inside participants
                $player = null;
                /** @var Participant $p */
                foreach ($info->getParticipants() as $p) {
                    if ($p->getPuuid() === $puuid) {
                        $player = $p;
                        break;
                    }
                }
                if (!$player) continue;

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

                $role = $player->getIndividualPosition();
                $pos = $player->getIndividualBest()["positive"];
                $neg = $player->getIndividualBest()["negative"];

                $rolePositive[$role] ??= [];
                $roleNegative[$role] ??= [];

                $roleScores[$role] = ($roleScores[$role] ?? 0) + $player->getScore();
                $roleCounts[$role] = ($roleCounts[$role] ?? 0) + 1;

                foreach ($pos as $stat => $value) {
                    $globalPositive[$stat] = ($globalPositive[$stat] ?? 0) + 1;

                    $rolePositive[$role][$stat] = ($rolePositive[$role][$stat] ?? 0) + 1;
                }

                foreach ($neg as $stat => $value) {
                    $globalNegative[$stat] = ($globalNegative[$stat] ?? 0) + 1;

                    $roleNegative[$role][$stat] = ($roleNegative[$role][$stat] ?? 0) + 1;
                }


                foreach ($rolePositive as $role => $stats) {
                    $topPerRole[$role]["positive"] = $this->top3($stats);
                }

                foreach ($roleNegative as $role => $stats) {
                    $topPerRole[$role]["negative"] = $this->top3($stats);
                }

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

}
