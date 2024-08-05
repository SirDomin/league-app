<?php

namespace App\Transformer;

use App\Entity\Challenge;

class ChallengeTransformer
{
    public static function getChallenge($data): Challenge
    {
        $challenge = new Challenge();
        $invalidFields = [];

        foreach ($data as $key => $challengeData) {
            if (method_exists($challenge::class, 'set'.$key)) {
                $challenge->{'set'.$key}($challengeData);
            } else {
                $invalidFields[] = [$key => $challengeData];
            }
        }

        return $challenge;
    }
}
