<?php

namespace App\Transformer;

use App\Entity\Info;
use App\Entity\Participant;

class InfoTransformer
{
    public static function getInfo($data): Info
    {
        $info = new Info();

        foreach ($data as $key => $infoData) {
            if ($key === 'participants') {
                foreach ($data['participants'] as $participant) {
                    $info->addParticipants(ParticipantTransformer::getParticipant($participant));
                }
                continue;
            }
            $info->{'set'.$key}($infoData);
        }

        return $info;
    }
}
