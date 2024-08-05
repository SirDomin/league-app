<?php

namespace App\Provider\Filter;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use App\Utils\StringUtils;

class ParticipantFilterProvider
{
    private array $fields = ['championId', 'individualPosition', 'role'];
    public function __construct(
        private readonly ParticipantRepository $participantRepository,
    )
    {

    }

    public function provideFilterForParticipant(): array
    {
        $participant = new \ReflectionClass(Participant::class);

        $filters = [];

        foreach ($participant->getProperties() as $property) {
            if (in_array($property->getName(), $this->fields)) {
                $columnType = StringUtils::extractColumnType($property->getDocComment());
                $filters[] = [
                    'property' => $property->getName(),
                    'type' => $columnType,
                    'data' => [
                        'fields' => $this->participantRepository->getDataForField($property->getName())
                    ],
                ];
            }
        }

        $filters[] = [
            'property' => 'matchId',
            'type' => 'string',
            'data' => [
                'sort' => false,
                'search' => true,
            ]
        ];


        return $filters;
    }
}
