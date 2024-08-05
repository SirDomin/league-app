<?php

namespace App\Calculator;

use App\Entity\Game;
use App\Entity\Participant;

class ScoreCalculator
{
    private array $weights = [
        'Kills' => 0.2,
        'Deaths' => -0.1, // negative weight because more deaths are worse
        'Assists' => 0.15,
        'TotalDamageDealtToChampions' => 0.2,
        'TotalDamageTaken' => 0.1,
        'TotalHeal' => 0.05,
        'GoldEarned' => 0.1,
        'ChampLevel' => 0.05,
        'VisionScore' => 0.05,
        'TimeCCingOthers' => 0.1
    ];

    public function __construct()
    {

    }

    public function calculateScoreForGame(Game $game): Game
    {
        $participants = $game->getInfo()->getParticipants();

        $participantsArray = $participants->toArray();

        foreach ($participantsArray as $participant) {
            $participant->setScore(0);

            foreach ($this->weights as $metric => $weight) {
                $getterMethod = 'get' . $metric;
                if (method_exists($participant, $getterMethod)) {
                    $value = $participant->$getterMethod();

                    $metricValues = array_map(function($p) use ($getterMethod) {
                        return $p->$getterMethod();
                    }, $participantsArray);
                    $maxValue = max($metricValues);
                    $minValue = min($metricValues);
                    $range = $maxValue - $minValue;

                    if ($range == 0) {
                        $normalizedValue = 1;
                    } else {
                        $normalizedValue = ($value - $minValue) / $range;
                    }

                    $normalizedValue = $normalizedValue * 100;

                    $participant->setScore($participant->getScore() + ($normalizedValue * $weight));
                }
            }
        }

        return $game;
    }
}
