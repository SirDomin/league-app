<?php

namespace App\Provider\Filter;

use App\Entity\Info;
use App\Entity\Metadata;
use App\Entity\Participant;
use App\Repository\InfoRepository;
use App\Repository\MetadataRepository;
use App\Utils\StringUtils;

class MetadataFilterProvider
{
    private array $fields = [];

    public function __construct(
        private readonly MetadataRepository $metadataRepository,
    )
    {
    }

    function provideFilterForMetadata(): array
    {
        $metadata = new \ReflectionClass(Metadata::class);

        $filters = [];

        foreach ($metadata->getProperties() as $property) {
            if (in_array($property->getName(), $this->fields)) {
                $columnType = StringUtils::extractColumnType($property->getDocComment());
                $filters[] = [
                    'property' => $property->getName(),
                    'type' => $columnType,
                    'data' => [
                        'fields' => $this->metadataRepository->getDataForField($property->getName())
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
