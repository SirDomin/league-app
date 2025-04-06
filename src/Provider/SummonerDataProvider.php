<?php

namespace App\Provider;

use App\ApiManager\LeagueApi;
use App\Entity\Participant;

class SummonerDataProvider
{
    public function __construct(
        private readonly LeagueApi $leagueApi,
    )
    {

    }

    public function getDataByName(string $gameName, string $gameTag): array
    {
        $data =  $this->leagueApi->getAccountDataByRiotId($gameName, $gameTag);

        return $this->leagueApi->getSummonerDataByPuuid($data['puuid']);
    }

    public function getPuuidByName(string $summonerName): string
    {
        $data = $this->leagueApi->getSummonerData($summonerName);

        return $data['puuid'];
    }

    public function getDataForParticipant(string $puuid): array
    {
        return $this->leagueApi->getAccountData($puuid);

    }
}
