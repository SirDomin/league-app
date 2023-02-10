<?php

namespace App\Provider;

use App\ApiManager\LeagueApi;
use App\Entity\Game;
use App\Entity\Metadata;
use App\Repository\GameRepository;
use App\Repository\ParticipantRepository;
use App\Transformer\InfoTransformer;

class GameProvider
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly LeagueApi $leagueApi,
        private readonly ParticipantRepository $participantRepository,
    )
    {
    }

    private function parseGame(?array $gameData): Game
    {
        $metadata = new Metadata();
        $metadata->setMatchId($gameData['metadata']['matchId']);
        $metadata->setDataVersion($gameData['metadata']['dataVersion']);
        $metadata->setParticipants($gameData['metadata']['participants']);
        $info = InfoTransformer::getInfo($gameData['info']);

        $game = new Game();
        $game->setInfo($info);
        $game->setMetadata($metadata);

        return $game;
    }

    public function connectParticipants($data): array
    {
        $participants = [];

        foreach ($data['participants'] as $participant) {
            $participants[] = [
                'games_played' => $this->gameRepository->countAllGamesWithPlayerBySummonerId($participant['summonerId']),
                'summoner_id' => $participant['summonerId'],
                'summoner_name' => $participant['summonerName'],
                'team_id' => $participant['teamId'],
                'champion_id' => $participant['championId'],
                'url_opgg' => 'https://www.op.gg/summoners/eune/' . $participant['summonerName'],
            ];
        }

        return $participants;
    }

    public function provideGameByMatchId(string $matchId): ?Game
    {
        $game = $this->gameRepository->findByMatchId($matchId);

        if ($game) {
            return $game;
        }

        return $this->parseGame($this->leagueApi->getGameById($matchId));
    }

    public function getLastGame(): ?Game
    {
    }

    public function provideActiveGameForUser(string $summonerName): ?array
    {
        $gameData = $this->leagueApi->getCurrentGame($summonerName);

        if ($gameData === null) {
            return null;
        }

        return $this->connectParticipants($gameData);
    }
}
