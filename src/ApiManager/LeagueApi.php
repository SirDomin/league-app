<?php

namespace App\ApiManager;

use Symfony\Component\HttpFoundation\JsonResponse;
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

    public function getGameById(string $matchId): array
    {
        $url = 'https://europe.api.riotgames.com/lol/match/v5/matches/' . $matchId;

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

        return json_decode($response->getContent(), true);
    }

}
