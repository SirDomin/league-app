<?php

namespace App\Provider;

use App\ApiManager\LeagueApi;

class SummonerDataProvider
{
    public function __construct(
        private readonly LeagueApi $leagueApi,
    )
    {

    }

    public function getDataByName(string $summonerName): array
    {
        return $this->leagueApi->getSummonerData($summonerName);
    }

    public function getPuuidByName(string $summonerName): string
    {
        $data = $this->leagueApi->getSummonerData($summonerName);

        return $data['puuid'];
    }
}
