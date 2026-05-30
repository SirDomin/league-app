<?php

namespace App\Calculator;

use App\Entity\Game;
use App\Entity\Participant;
use App\Repository\ParticipantRepository;

class ScoreCalculator
{
    private array $weights = [
        'Kills' => 6,
        'Deaths' => -8,
        'Assists' => 4,
        'GoldEarned' => 8,
        'ChampLevel' => 4,
        'TotalDamageDealtToChampions' => 6,
        'VisionScore' => 3,
        'DamageDealtToBuildings' => 3,
    ];

    private array $challengeWeights = [
        'EarlyLaningPhaseGoldExpAdvantage' => 3,
        'MaxCsAdvantageOnLaneOpponent' => 0.1,
        'MaxLevelLeadLaneOpponent' => 2,
        'KillParticipation' => 5,
        'TeamDamagePercentage' => 10,
        'TurretPlatesTaken' => 1,
        'TakedownOnFirstTurret' => 2,
    ];

    private array $challengeCaps = [
        'EarlyLaningPhaseGoldExpAdvantage' => 3,
        'MaxCsAdvantageOnLaneOpponent' => 3,
        'MaxLevelLeadLaneOpponent' => 4,
        'KillParticipation' => 5,
        'TeamDamagePercentage' => 10,
        'TurretPlatesTaken' => 5,
        'TakedownOnFirstTurret' => 2,
    ];

    private array $individualBestMetrics = [
        'DamageTakenOnTeamPercentage',
        'KillParticipation',
        'SkillshotsDodged',
        'TeamDamagePercentage',
        'VisionScorePerMinute',
        'EarlyLaningPhaseGoldExpAdvantage',
        'ImmobilizeAndKillWithAlly',
        'JunglerTakedownsNearDamagedEpicMonster',
        'KillAfterHiddenWithAlly',
        'KillsWithHelpFromEpicMonster',
        'LandSkillShotsEarlyGame',
        'MaxCsAdvantageOnLaneOpponent',
        'MaxLevelLeadLaneOpponent',
        'OuterTurretExecutesBefore10Minutes',
        'OutnumberedKills',
        'PerfectGame',
        'QuickCleanse',
        'QuickSoloKills',
        'SaveAllyFromDeath',
        'SoloKills',
        'TakedownOnFirstTurret',
        'ControlWardsPlaced',
        'DodgeSkillShotsSmallWindow',
        'EpicMonsterKillsNearEnemyJungler',
        'EpicMonsterKillsWithin30SecondOfSpawn',
        'KillsNearEnemyTurret',
        'ThreeWardsOneSweeperCount',
        'TurretPlatesTaken',
        'UnseenRecalls',
        'WardsGuarded',
    ];

    private array $junglerChallenges = [
        'BuffsStolen' => 0.5,
    ];

    private const MAX_CHALLENGE_BONUS = 15;
    private const MIN_INDIVIDUAL_BEST_DIFFERENCE = 5;

    private array $challengeAverages = [];

    public function __construct(
        private readonly ParticipantRepository $participantRepository,
    ) {}

    private function toSnakeCase(array $array): array
    {
        $snakeCaseArray = [];
        foreach ($array as $key => $value) {
            $snakeKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
            $snakeCaseArray[$snakeKey] = $value;
        }
        return $snakeCaseArray;
    }

    private function stringToSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    public function getCalculatableScore(): array
    {
        return [
            'jungle' => $this->toSnakeCase($this->junglerChallenges),
            'challenges' => $this->toSnakeCase($this->challengeWeights),
            'regular' => $this->toSnakeCase($this->weights),
        ];
    }

    public function calculateScoreForGame(Game $game): Game
    {
        $participants = $game->getInfo()->getParticipants();

        try {
            $participantsArray = $participants->toArray();

        } catch (\Throwable $exception) {
            $participantsArray = $participants;
        }

        $individualBest = [];
        /** @var Participant $participant */
        foreach ($participantsArray as $participant) {
            $participant->setScore(0);
            $score = 50.0;
            $challengeBonus = 0.0;
            $individualBest[$participant->getPuuid()] = [];

            foreach ($this->weights as $metric => $weight) {
                $getterMethod = 'get' . $metric;

                if (method_exists($participant, $getterMethod)) {
                    $value = $participant->$getterMethod();

                    $opponentValues = array_map(function($p) use ($getterMethod, $participant) {
                        if (
                            $p->getTeamId() !== $participant->getTeamId()
                            && $this->getPositionKey($p) === $this->getPositionKey($participant)
                        ) {
                            return $p->$getterMethod();
                        }

                        return null;
                    }, $participantsArray);

                    $opponentValues = array_filter($opponentValues, function($value) {
                        return $value !== null;
                    });

                    if ($opponentValues === []) {
                        continue;
                    }

                    $opponentValue = array_sum($opponentValues) / count($opponentValues);
                    $advantage = ($value - $opponentValue) / max(abs($value), abs($opponentValue), 1);

                    $score += $advantage * $weight;
                }
            }

            $challenge = $participant->getChallenges();

            if ($challenge) {
                $position = $this->getPositionKey($participant);
                $challengeAverages = $this->getChallengeAverages($position, $game->getInfo()->getQueueId());

                foreach ($this->challengeWeights as $metric => $weight) {
                    $getterMethod = 'get' . $metric;
                    if (method_exists($challenge, $getterMethod)) {
                        $value = $challenge->$getterMethod();
                        $challengeBonus += min($this->challengeCaps[$metric], max(0, $value * $weight));
                    }
                }

                foreach ($this->individualBestMetrics as $metric) {
                    $getterMethod = 'get' . $metric;
                    if (method_exists($challenge, $getterMethod)) {
                        $average = $challengeAverages[lcfirst($metric)] ?? null;
                        if ($average !== null) {
                            $individualBest[$participant->getPuuid()][$this->stringToSnakeCase($metric)] =
                                $this->calculateDifferenceFromAverage($challenge->$getterMethod(), $average);
                        }
                    }
                }

                if ($participant->getIndividualPosition() === 'JUNGLE') {
                    foreach ($this->junglerChallenges as $metric => $weight) {
                        $getterMethod = 'get' . $metric;
                        if (method_exists($challenge, $getterMethod)) {
                            $value = $challenge->$getterMethod();
                            $average = $challengeAverages[lcfirst($metric)] ?? null;
                            if ($average !== null) {
                                $individualBest[$participant->getPuuid()][$this->stringToSnakeCase($metric)] =
                                    $this->calculateDifferenceFromAverage($value, $average);
                            }

                            $challengeBonus += min(2, max(0, $value * $weight));
                        }
                    }
                }
            }

            $score += min(self::MAX_CHALLENGE_BONUS, $challengeBonus);
            $participant->setScore((int) round(max(0, min(100, $score))));
            $participant->setIndividualBest($individualBest[$participant->getPuuid()]);
        }

        /** @var Participant $participant */
        foreach ($participantsArray as $participant) {
            $participant->setIndividualBest($this->overrideIndividualBest($participant));
        }

        return $game;
    }

    private function getPositionKey(Participant $participant): string
    {
        return $participant->getIndividualPosition()
            ?: $participant->getTeamPosition()
            ?: 'UNASSIGNED:' . $participant->getPuuid();
    }

    private function calculateDifferenceFromAverage(float|int $value, float $average): float
    {
        $difference = ($value - $average) / max(abs($average), 1);

        return round(max(-1, min(1, $difference)) * 100, 2);
    }

    private function getChallengeAverages(string $position, ?int $queueId): array
    {
        $key = $position . ':' . ($queueId ?? 'all');

        return $this->challengeAverages[$key] ??= $this->participantRepository
            ->getChallengeAveragesForPosition(
                $position,
                $queueId,
                array_unique(array_merge($this->individualBestMetrics, array_keys($this->junglerChallenges)))
            );
    }

    private function overrideIndividualBest(Participant $participant): array
    {
        $individualBest = $participant->getIndividualBest();

        $positiveValues = array_filter($individualBest, function($value) {
            return $value >= self::MIN_INDIVIDUAL_BEST_DIFFERENCE;
        });
        $negativeValues = array_filter($individualBest, function($value) {
            return $value <= -self::MIN_INDIVIDUAL_BEST_DIFFERENCE;
        });
        $goodScores = 3;
        $badScores = 3;

        $score = $participant->getScore();
        if ($score < 35) {
            $goodScores = 1;
            $badScores = 5;
        } else if ($score > 65) {
            $goodScores = 5;
            $badScores = 1;
        } else if ($score < 45) {
            $goodScores = 2;
            $badScores = 4;
        } else if ($score > 55) {
            $goodScores = 4;
            $badScores = 2;
        }

        arsort($positiveValues);
        $top5 = array_slice($positiveValues, 0, $goodScores, true);

        asort($negativeValues);
        $bottom5 = array_slice($negativeValues, 0, $badScores, true);

        return ['positive' => $top5, 'negative' => $bottom5];
    }
}
