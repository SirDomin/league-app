<?php

namespace App\Calculator;

use App\Entity\Game;
use App\Entity\Participant;

class ScoreCalculator
{
    private array $weights = [
        'Kills' => 0.2,
        'Deaths' => -0.1,
        'Assists' => 0.15,
        'TotalDamageDealtToChampions' => 0.2,
        'TotalDamageTaken' => 0.1,
        'TotalHeal' => 0.05,
        'GoldEarned' => 0.1,
        'ChampLevel' => 0.05,
        'VisionScore' => 0.05,
        'DamageDealtToBuildings' => 0.05,
    ];

    private array $challengeWeights = [
        'DamageTakenOnTeamPercentage' => 25,
        'KillParticipation' => 10,
        'SkillshotsDodged' => 0.05,
        'TeamDamagePercentage' => 40,
        'VisionScorePerMinute' => 1,
        'EarlyLaningPhaseGoldExpAdvantage' => 10,
        'ImmobilizeAndKillWithAlly' => 1,
        'JunglerTakedownsNearDamagedEpicMonster' => 1,
        'KillAfterHiddenWithAlly' => 1,
        'KillsWithHelpFromEpicMonster' => 1,
        'LandSkillShotsEarlyGame' => 1,
        'MaxCsAdvantageOnLaneOpponent' => 0.2,
        'MaxLevelLeadLaneOpponent' => 1,
        'OuterTurretExecutesBefore10Minutes' => 1,
        'OutnumberedKills' => 1,
        'PerfectGame' => 5,
        'QuickCleanse' => 3,
        'QuickSoloKills' => 2,
        'SaveAllyFromDeath' => 1,
        'SoloKills' => 1,
        'TakedownOnFirstTurret' => 3,
        'ControlWardsPlaced' => 1,
        'DodgeSkillShotsSmallWindow' => 1,
        'EpicMonsterKillsNearEnemyJungler' => 1,
        'EpicMonsterKillsWithin30SecondOfSpawn' => 1,
        'KillsNearEnemyTurret' => 1,
        'ThreeWardsOneSweeperCount' => 1,
        'TurretPlatesTaken' => 1,
        'UnseenRecalls' => 1,
        'WardsGuarded' => 1
    ];

    private array $roleOrder = [
        'TOP' => 1,
        'JUNGLE' => 2,
        'MID' => 3,
        'BOTTOM' => 4,
        'UTILITY' => 5
    ];

    private array $junglerChallenges = [
        'BuffsStolen' => 0.5,
//        'JungleCSBefore10Minutes' => 0.25,
    ];

    public function __construct()
    {

    }

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
        $rawScores = [];

        /** @var Participant $participant */
        foreach ($participantsArray as $participant) {
            $participant->setScore(0);
            $score = 0.0;
            $individualBest[$participant->getPuuid()] = [];

            foreach ($this->weights as $metric => $weight) {
                $getterMethod = 'get' . $metric;

                if (method_exists($participant, $getterMethod)) {
                    $value = $participant->$getterMethod();

                    $metricValues = array_map(function($p) use ($getterMethod, $participant) {
                        if (
                            $p === $participant
                            || (
                                $p->getTeamId() !== $participant->getTeamId()
                                && $this->getPositionKey($p) === $this->getPositionKey($participant)
                            )
                        ) {
                            return $p->$getterMethod();
                        }

                        return null;
                    }, $participantsArray);

                    $metricValues = array_filter($metricValues, function($value) {
                        return $value !== null;
                    });

                    $maxValue = max($metricValues);
                    $minValue = min($metricValues);
                    $range = $maxValue - $minValue;

                    if ($range == 0) {
                        $normalizedValue = 0.5;
                    } else {
                        $normalizedValue = ($value - $minValue) / $range;
                    }

                    $normalizedValue = $normalizedValue * 100;

                    $score += $normalizedValue * $weight;
                }
            }

            $challenge = $participant->getChallenges();

            if ($challenge) {
                foreach ($this->challengeWeights as $metric => $weight) {
                    $getterMethod = 'get' . $metric;
                    if (method_exists($challenge, $getterMethod)) {
                        $value = $challenge->$getterMethod();
                        if ($this->metricCalculate($metric)) {
                            $individualBest[$participant->getPuuid()][$this->stringToSnakeCase($metric)] = ($value * $weight);
                        }

                        $score += $value * $weight;
                    }
                }

                if ($participant->getIndividualPosition() === 'JUNGLE') {
                    foreach ($this->junglerChallenges as $metric => $weight) {
                        $getterMethod = 'get' . $metric;
                        if (method_exists($challenge, $getterMethod)) {
                            $value = $challenge->$getterMethod();
                            if ($this->metricCalculate($metric)) {
                                $individualBest[$participant->getPuuid()][$this->stringToSnakeCase($metric)] = ($value * $weight);
                            }

                            $score += $value * $weight;
                        }
                    }
                }
            }

            $rawScores[$participant->getPuuid()] = max(0, $score);
            $participant->setIndividualBest($individualBest[$participant->getPuuid()]);
        }

        $positionScores = [];
        $positionCounts = [];
        foreach ($participantsArray as $participant) {
            $position = $this->getPositionKey($participant);
            $positionScores[$position] = ($positionScores[$position] ?? 0) + $rawScores[$participant->getPuuid()];
            $positionCounts[$position] = ($positionCounts[$position] ?? 0) + 1;
        }

        foreach ($participantsArray as $participant) {
            $position = $this->getPositionKey($participant);
            $positionScore = $positionScores[$position];
            if ($positionCounts[$position] === 1) {
                $scaledScore = $rawScores[$participant->getPuuid()];
            } else if ($positionScore == 0) {
                $scaledScore = 100 / $positionCounts[$position];
            } else {
                $scaledScore = ($rawScores[$participant->getPuuid()] / $positionScore) * 100;
            }
            $participant->setScore(round($scaledScore));
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

    private function metricCalculate(string $metric): bool
    {
        $nonCalculate = [
            'VisionScorePerMinute',
            'SkillshotsDodged',
        ];

        return !in_array($metric, $nonCalculate);
    }

    private function overrideIndividualBest(Participant $participant): array
    {
        $individualBest = $participant->getIndividualBest();

        $nonZeroValues = array_filter($individualBest, function($value) {
            return $value !== 0;
        });
        $goodScores = 3;
        $badScores = 3;

        $score = $participant->getScore();
        if ($score < 10) {
            $goodScores = 1;
            $badScores = 5;
        } else if ($score > 30) {
            $goodScores = 5;
            $badScores = 1;
        } else if ($score < 15) {
            $goodScores = 2;
            $badScores = 4;
        } else if ($score > 25) {
            $goodScores = 4;
            $badScores = 2;
        }

        arsort($nonZeroValues);
        $top5 = array_slice($nonZeroValues, 0, $goodScores, true);

        asort($nonZeroValues);
        $bottom5 = array_slice($nonZeroValues, 0, $badScores, true);

        return ['positive' => $top5, 'negative' => $bottom5];
    }
}
