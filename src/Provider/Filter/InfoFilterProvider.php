<?php

namespace App\Provider\Filter;

use App\Entity\Info;
use App\Entity\Participant;
use App\Repository\GameRepository;
use App\Repository\InfoRepository;
use App\Utils\StringUtils;

class InfoFilterProvider
{
    private array $fields = ['queueId'];

    public function __construct(
        private readonly InfoRepository $infoRepository,
        private readonly GameRepository $gameRepository,
    )
    {
    }

    function provideFilterForInfo(): array
    {
        $participant = new \ReflectionClass(Info::class);

        $filters = [];

        foreach ($participant->getProperties() as $property) {
            if (in_array($property->getName(), $this->fields)) {
                $columnType = StringUtils::extractColumnType($property->getDocComment());
                $filters[] = [
                    'property' => $property->getName(),
                    'type' => $columnType,
                    'data' => [
                        'fields' => $this->infoRepository->getDataForField($property->getName())
                    ],
                ];
            }
        }

        $filters[] = [
            'property' => 'gameDuration',
            'type' => 'integer',
            'data' => [
                'sort' => true,
                'search' => false,
            ]
        ];

        $filters[] = [
            'property' => 'gameCreationDate',
            'type' => 'date',
            'data' => [
                'sort' => true,
                'search' => false,
            ]
        ];

        $filters[] = [
            'property' => 'season',
            'type' => 'date',
            'data' => [
                'fields' => $this->gameRepository->getAvailableSeasons()
            ],
        ];

        return $filters;
    }
}
