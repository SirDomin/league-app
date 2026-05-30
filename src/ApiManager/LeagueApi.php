<?php

namespace App\ApiManager;

use App\Exception\ApiRateExceededException;
use App\Utils\RegionMatcher;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LeagueApi
{
    private string $token;

    private string $secret;

    private string $iv;

    private string $cypherMethod;

    private ?string $serverName;

    private FilesystemAdapter $cache;

    public function __construct(
        private readonly HttpClientInterface $httpClient
    )
    {
        $this->cache = new FilesystemAdapter();
        $this->token = $_ENV['APP_RIOT_TOKEN'];
        $this->secret = $_ENV['APP_SECRET'];
        $this->cypherMethod = 'AES-256-CBC';
        $this->iv = base64_decode($_ENV['APP_IV']);

        $this->serverName = 'eun1';
    }

    public function setServer(string $server) {
        $this->serverName = $server;
    }

    public function getServer() {
        return $this->serverName;
    }
    public function decodeKey(string $data) {
        return json_decode(openssl_decrypt($data, $this->cypherMethod, $this->secret, $options = 0, $this->iv), true);
    }

    public function encodeKey(array $data): string {
        return openssl_encrypt(json_encode($data), $this->cypherMethod, $this->secret, $options=0, $this->iv);
    }

    private function getRequest(string $url, bool $allowCache = true, $cacheTime = 3600): array
    {
        $cacheKey = $this->sanitizeString($url);
        $cacheItem = $this->cache->getItem($cacheKey);

        if ($allowCache === true) {
            if ($cacheItem->isHit()) {
                $resp = $cacheItem->get();
                if (is_array($resp) && sizeof($resp) > 0) {
                    $resp['cached'] = true;

                    return $resp;
                }

            }
        }

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
            throw new ApiRateExceededException();
        }

        if ($response->getStatusCode() === 200) {
            $resp = json_decode($response->getContent(false), true);
            $resp['cached'] = false;

            $cacheItem->set($resp);
            $cacheItem->expiresAfter($cacheTime);

            $this->cache->save($cacheItem);
        } else {
            return [];
        }

        return $resp;
    }

    public function getSummonerData(string $summonerName): array
    {
        $url = 'https://' . $this->serverName . '.api.riotgames.com/lol/summoner/v4/summoners/by-name/' . $summonerName;

        return $this->getRequest($url);
    }

    public function login(string $summonerName, string $tag, string $region): array
    {
        $serverName = RegionMatcher::matchRegionToServer($region);

        $url = 'https://'. $serverName .'.api.riotgames.com/riot/account/v1/accounts/by-riot-id/' . $summonerName . '/' . $tag;
        $data = $this->getRequest($url);

        return array_merge($data, $this->getAccountData($data['puuid']));
    }

    public function getSummonerDataByPuuid(string $puuid, bool $cache = true, ?string $server = null): array
    {
        if ($server === null) {
            $url = 'https://' . $this->serverName . '.api.riotgames.com/lol/summoner/v4/summoners/by-puuid/' . $puuid;
        } else {
            $url = 'https://' . strtolower($server) . '.api.riotgames.com/lol/summoner/v4/summoners/by-puuid/' . $puuid;

        }

        $data = $this->getRequest($url, $cache);

        return array_merge($data, $this->getAccountData($puuid, $cache));
    }

    public function getChampionMasteryByChampionId(string $puuid, int $championId): array
    {
        $url = 'https://' . $this->serverName . '.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-puuid/'.$puuid.'/by-champion/'.$championId;

        return $this->getRequest($url, true, 36000);
    }

    public function getAccountData(string $puuid, bool $cache = true): array
    {
        $url = 'https://europe.api.riotgames.com/riot/account/v1/accounts/by-puuid/' . $puuid;

        return $this->getRequest($url, $cache);
    }

    public function getAccountDataByRiotId(string $gameName, string $gameTag): array
    {
        $url = 'https://europe.api.riotgames.com/riot/account/v1/accounts/by-riot-id/' . $gameName . '/' . $gameTag;

        return $this->getRequest($url);
    }

    public function getSummonerLeagues(string $summonerId): array
    {
        $url = \sprintf('https://' . $this->serverName . '.api.riotgames.com/lol/league/v4/entries/by-summoner/%s', $summonerId);

        return $this->getRequest($url);
    }

    public function getGameById(string $matchId): array
    {
        $url = 'https://europe.api.riotgames.com/lol/match/v5/matches/' . $matchId;

        return $this->getRequest($url);
    }

    public function getTimelineForMatchId(string $matchId): array
    {
        $url = 'https://europe.api.riotgames.com/lol/match/v5/matches/' . $matchId . '/timeline';

        return $this->getRequest($url);
    }

    public function getGamesHistory(string $puuid, int $limit, int $start): array
    {
        $url = 'https://europe.api.riotgames.com/lol/match/v5/matches/by-puuid/' . $puuid . '/ids?start=' . $start . '&count=' . $limit;

        $response = $this->getRequest($url, true, 60);

        return $response;
    }

    public function getCurrentGame(string $summonerName): ?array
    {
        $summonerData = $this->getSummonerData($summonerName);

        $url = 'https://' . $this->serverName . '.api.riotgames.com/lol/spectator/v4/active-games/by-summoner/' . $summonerData['id'];

        $response = $this->getRequest($url, false);

        if (isset($response['status']) && $response['status']['status_code'] === 404) {
            return null;
        }

        $response['summonerData'] = $summonerData;

        return $response;
    }

    public function getCurrentGameForUser(string $puuid): ?array
    {

        $url = 'https://' . $this->serverName . '.api.riotgames.com/lol/spectator/v5/active-games/by-summoner/' . $puuid;

        $response = $this->getRequest($url, false);

        if (isset($response['status']) && $response['status']['status_code'] === 404) {
            return null;
        }

        return $response;
    }


    private function sanitizeString($input) {
        $pattern = '/[{}()\/@:]/';
        $sanitized = preg_replace($pattern, '', $input);
        return $sanitized;
    }

    public function getAccountByRiotId(string $name, string $tag)
    {
        return $this->getRequest("https://europe.api.riotgames.com/riot/account/v1/accounts/by-riot-id/$name/$tag");
    }

    public function getSummonerByPuuid(string $puuid, string $region)
    {
        return $this->getRequest("https://$region.api.riotgames.com/lol/summoner/v4/summoners/by-puuid/$puuid");
    }

    public function getRankedStats(string $puuid, $region)
    {
        return $this->getRequest("https://$region.api.riotgames.com/lol/league/v4/entries/by-puuid/$puuid");
    }

    public function getRankedMatchIds(string $puuid, int $count, string $region)
    {
        $server = RegionMatcher::matchRegionToServer($region);

        return $this->getRequest("https://$server.api.riotgames.com/lol/match/v5/matches/by-puuid/$puuid/ids?type=ranked&count=$count");
    }

    public function getMatch(string $matchId, string $region)
    {
        $server = RegionMatcher::matchRegionToServer($region);

        return $this->getRequest("https://$server.api.riotgames.com/lol/match/v5/matches/$matchId");
    }

    public function getGamesHistoryByDate(string $puuid, string $region, \DateTime $startTime, \DateTime $endTime, $limit = 10): array
    {
        $server = RegionMatcher::matchRegionToServer($region);

        $baseUrl = 'https://' . $server . '.api.riotgames.com/lol/match/v5/matches/by-puuid/' . $puuid . '/ids';

        $query = http_build_query([
            'startTime' => $startTime->getTimestamp(),
            'endTime'   => $endTime->getTimestamp(),
            'count'     => (int) $limit,
        ]);

        $url = $baseUrl . '?' . $query;

        $response = $this->getRequest($url, true, 60);

        return $response;
    }

    public function getSummonerLeaguesForServer(string $puuid, string $region): array
    {
        $url = \sprintf('https://' . $region . '.api.riotgames.com/lol/league/v4/entries/by-puuid/%s', $puuid);

        return $this->getRequest($url);
    }
}
