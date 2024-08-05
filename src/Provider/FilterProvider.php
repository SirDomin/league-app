<?php

namespace App\Provider;

use App\Provider\Filter\GameFilterProvider;
use App\Provider\Filter\InfoFilterProvider;
use App\Provider\Filter\MetadataFilterProvider;
use App\Provider\Filter\ParticipantFilterProvider;

class FilterProvider
{
    public function __construct(
        private readonly GameFilterProvider $gameFilterProvider,
        private readonly InfoFilterProvider $infoFilterProvider,
        private readonly  ParticipantFilterProvider $participantFilterProvider,
        private readonly MetadataFilterProvider $metadataFilterProvider
    )
    {
    }

    public function getFiltersForParticipant(): array
    {
        return $this->participantFilterProvider->provideFilterForParticipant();
    }

    public function getFiltersForInfo(): array
    {
        return $this->infoFilterProvider->provideFilterForInfo();

    }

    public function getFiltersForMetadata(): array
    {
        return $this->metadataFilterProvider->provideFilterForMetadata();
    }

    public function getFiltersForGame(): array
    {
        return $this->gameFilterProvider->provideFilterForGame();

    }

    public function getAllFilters(): array
    {
        return [
            'game' => $this->getFiltersForGame(),
            'info' => $this->getFiltersForInfo(),
            'participant' => $this->getFiltersForParticipant(),
            'metadata' => $this->getFiltersForMetadata(),
        ];
    }
}
