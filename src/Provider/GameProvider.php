<?php

namespace App\Provider;

use App\ApiManager\LeagueApi;
use App\Entity\Game;
use App\Entity\Metadata;
use App\Repository\GameRepository;
use App\Transformer\InfoTransformer;

class GameProvider
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly LeagueApi $leagueApi
    )
    {
    }

    public function provideGameByMatchId(string $matchId): ?Game
    {
        $game = $this->gameRepository->findByMatchId($matchId);

        if ($game) {
            return $game;
        }

        $game = $this->leagueApi->getGameById($matchId);

        $metadata = new Metadata();
        $metadata->setMatchId($game['metadata']['matchId']);
        $metadata->setDataVersion($game['metadata']['dataVersion']);
        $metadata->setParticipants($game['metadata']['participants']);
        $info = InfoTransformer::getInfo($game['info']);

        $game = new Game();
        $game->setInfo($info);
        $game->setMetadata($metadata);

        return $game;
    }
}
