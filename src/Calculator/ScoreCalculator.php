<?php

namespace App\Calculator;

use App\Entity\Game;
use App\Entity\Participant;

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

    private array $individualBestWeights = [
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

    private array $junglerChallenges = [
        'BuffsStolen' => 0.5,
    ];

    private const MAX_CHALLENGE_BONUS = 15;

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
                foreach ($this->challengeWeights as $metric => $weight) {
                    $getterMethod = 'get' . $metric;
                    if (method_exists($challenge, $getterMethod)) {
                        $value = $challenge->$getterMethod();
                        $challengeBonus += min($this->challengeCaps[$metric], max(0, $value * $weight));
                    }
                }

                foreach ($this->individualBestWeights as $metric => $weight) {
                    $getterMethod = 'get' . $metric;
                    if (method_exists($challenge, $getterMethod) && $this->metricCalculate($metric)) {
                        $individualBest[$participant->getPuuid()][$this->stringToSnakeCase($metric)] = $challenge->$getterMethod() * $weight;
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

        arsort($nonZeroValues);
        $top5 = array_slice($nonZeroValues, 0, $goodScores, true);

        asort($nonZeroValues);
        $bottom5 = array_slice($nonZeroValues, 0, $badScores, true);

        return ['positive' => $top5, 'negative' => $bottom5];
    }
}
