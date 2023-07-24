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
            $division = $this->leagueApi->getSummonerLeagues($participant['summonerId']);

            $ranking = [];

            foreach ($division as $div) {
                $ranking[] = [
                    $div['queueType'] => \sprintf('%s %s ( %d lp)', $div['tier'] ?? '', $div['rank'] ?? '', $div['leaguePoints'] ?? '')
                ];
            }

            $participants[] = [
                'games_played' => $this->gameRepository->countAllGamesWithPlayerBySummonerId($participant['summonerId']),
                'summoner_id' => $participant['summonerId'],
                'summoner_name' => $participant['summonerName'],
                'team_id' => $participant['teamId'],
                'champion_id' => $participant['championId'],
                'url_opgg' => 'https://www.op.gg/summoners/eune/' . $participant['summonerName'],
                'division' => $ranking,
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

    public function getHistory(string $summonerName, int $limit = 0, int $start = 0, int $lastTimestamp = 0): ?array
    {
        $lastGames = $this->leagueApi->getGamesHistory($summonerName, $limit, $start);

        $gamesResult = [];

        foreach ($lastGames as $gameId) {
            $game = $this->gameRepository->findByMatchId($gameId);

            if (!$game) {
                $gamesResult[] = [
                    'gameId' => $gameId,
                ];
            } else {
                $gamesResult[] = $game;
            }
        }

        if (count($gamesResult) < 50) {
            if ($gamesResult !== []) {
                /** @var Game $lastGame */
                $lastGame = end($gamesResult);

                $lastTimestamp = $lastGame->getInfo()->getGameStartTimestamp();
            }

            $games = $this->gameRepository->paginateHistory($lastTimestamp, $limit - count($gamesResult));

            foreach ($games as $game) {
                $gamesResult[] = $game;
            }
        }



        return $gamesResult;
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
