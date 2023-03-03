<?php

namespace App\ApiManager;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class LeagueApi
{
    private string $token;

    public function __construct(
        private readonly HttpClientInterface $httpClient
    )
    {
        $this->token = $_ENV['APP_RIOT_TOKEN'];
    }

    private function getRequest(string $url): array
    {
        $headers = [
            'X-Riot-Token' => $this->token
        ];

        $response = $this->httpClient->request(
            'GET',
            $url,
            [
                'headers' => $headers,
            ]
        );

        if ($response->getStatusCode() === 429) {
            throw new \Exception('API Rate exceeded');
        }

        return json_decode($response->getContent(false), true);
    }

    public function getSummonerData(string $summonerName): array
    {
        $url = 'https://eun1.api.riotgames.com/lol/summoner/v4/summoners/by-name/' . $summonerName;

        return $this->getRequest($url);
    }

    public function getSummonerLeagues(string $summonerId): array
    {
        $url = \sprintf('https://eun1.api.riotgames.com/lol/league/v4/entries/by-summoner/%s', $summonerId);

        return $this->getRequest($url);
    }

    public function getGameById(string $matchId): array
    {
        $url = 'https://europe.api.riotgames.com/lol/match/v5/matches/' . $matchId;

        return $this->getRequest($url);
    }

    public function getGamesHistory(string $summonerName, int $limit, int $start): array
    {
        $summonerData = $this->getSummonerData($summonerName);

        $url = 'https://europe.api.riotgames.com/lol/match/v5/matches/by-puuid/' . $summonerData['puuid'] . '/ids?start=' . $start . '&count=' . $limit;

        $response = $this->getRequest($url);

        return $response;
    }

    public function getCurrentGame(string $summonerName): ?array
    {
        $summonerData = $this->getSummonerData($summonerName);

        $url = 'https://eun1.api.riotgames.com/lol/spectator/v4/active-games/by-summoner/' . $summonerData['id'];

        $response = $this->getRequest($url);

        if (isset($response['status']) && $response['status']['status_code'] === 404) {
            return null;
        }

        return $response;
    }

}
