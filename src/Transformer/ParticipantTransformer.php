<?php

namespace App\Transformer;

use App\Entity\Participant;

class ParticipantTransformer
{
    public static function getParticipant($data): Participant
    {
        $participant = new Participant();

        foreach ($data as $key => $participantData) {
            if (method_exists($participant, 'set'.$key)) {
                $participant->{'set'.$key}($participantData);

            }

        }

        return $participant;
    }
}
