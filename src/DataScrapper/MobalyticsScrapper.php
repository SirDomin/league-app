<?php

declare(strict_types=1);

namespace App\DataScrapper;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MobalyticsScrapper
{
    private const GRAPHQL_URL = 'https://mobalytics.gg/api/lol/v1/graphql/query';

    private const PROFILE_QUERY = <<<'GRAPHQL'
query ActiveGameMobalyticsProfile($region: Region!, $tagLine: String!, $gameName: String!) {
  lol {
    player(region: $region, tagLine: $tagLine, gameName: $gameName) {
      gameName
      tagLine
      badges(filter: { isProfilesOnly: true }) {
        items {
          slug
          name
          description
          kind
          type
        }
      }
      queuesStats {
        items {
          rank {
            tier
            division
          }
          queue: virtualQueue
          lp
          wins
          winrate
          gamesCount
          losses
        }
      }
      roleStats(filter: {}) {
        defaultRole {
          wins
          looses
          kda {
            kills
            deaths
            assists
          }
          csm
          kp
          lp
        }
      }
      gpi(filter: {}) {
        items {
          type
          value
        }
      }
      performanceMetrics(filter: {}) {
        items {
          type
          value
          position
        }
      }
    }
  }
}
GRAPHQL;

    private FilesystemAdapter $cache;

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
        $this->cache = new FilesystemAdapter('mobalytics_profiles', 0);
    }

    /**
     * @param string[] $riotIds
     *
     * @return array<string, array>
     */
    public function getProfiles(array $riotIds, string $server): array
    {
        $region = $this->normalizeServer($server);
        $profiles = [];
        $requests = [];

        foreach (array_unique($riotIds) as $riotId) {
            $account = $this->splitRiotId($riotId);

            if ($account === null) {
                continue;
            }

            [$gameName, $tagLine] = $account;
            $key = $this->getProfileKey($gameName, $tagLine);
            $profileUrl = $this->getProfileUrl($region, $gameName, $tagLine);
            $cacheItem = $this->cache->getItem(hash('sha256', $region . '|' . $key));

            if ($cacheItem->isHit()) {
                $profiles[$key] = $cacheItem->get();
                continue;
            }

            try {
                $requests[$key] = [
                    'cache_item' => $cacheItem,
                    'profile_url' => $profileUrl,
                    'response' => $this->httpClient->request('POST', self::GRAPHQL_URL, [
                        'headers' => [
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/122.0.0.0 Safari/537.36',
                        ],
                        'json' => [
                            'operationName' => 'ActiveGameMobalyticsProfile',
                            'variables' => [
                                'region' => strtoupper($region),
                                'gameName' => $gameName,
                                'tagLine' => $tagLine,
                            ],
                            'query' => self::PROFILE_QUERY,
                        ],
                        'timeout' => 5,
                    ]),
                ];
            } catch (TransportExceptionInterface) {
                $cacheItem->set($this->getEmptyProfile($profileUrl));
                $cacheItem->expiresAfter(300);
                $this->cache->save($cacheItem);
                $profiles[$key] = $cacheItem->get();
            }
        }

        foreach ($requests as $key => $request) {
            $profile = $this->getProfileFromResponse($request['response'], $request['profile_url']);
            $request['cache_item']->set($profile);
            $request['cache_item']->expiresAfter($profile['available'] ? 1800 : 300);
            $this->cache->save($request['cache_item']);
            $profiles[$key] = $profile;
        }

        return $profiles;
    }

    public function getProfileUrl(string $server, string $gameName, string $tagLine): string
    {
        return sprintf(
            'https://mobalytics.gg/lol/profile/%s/%s/overview',
            $this->normalizeServer($server),
            rawurlencode($gameName . '-' . $tagLine),
        );
    }

    public function getProfileKey(string $gameName, string $tagLine): string
    {
        return strtolower(trim($gameName) . '#' . trim($tagLine));
    }

    /**
     * @return array{0: string, 1: string}|null
     */
    public function splitRiotId(string $riotId): ?array
    {
        if (!str_contains($riotId, '#')) {
            return null;
        }

        [$gameName, $tagLine] = explode('#', $riotId, 2);
        $gameName = trim($gameName);
        $tagLine = trim($tagLine);

        return $gameName !== '' && $tagLine !== '' ? [$gameName, $tagLine] : null;
    }

    private function getProfileFromResponse(ResponseInterface $response, string $profileUrl): array
    {
        $emptyProfile = $this->getEmptyProfile($profileUrl);

        try {
            if ($response->getStatusCode() !== 200) {
                return $emptyProfile;
            }

            $content = $response->getContent(false);

            if (
                str_contains($content, 'challenges.cloudflare.com')
                || str_contains($content, 'Enable JavaScript and cookies to continue')
            ) {
                return $emptyProfile;
            }

            $player = json_decode($content, true)['data']['lol']['player'] ?? null;

            if (!is_array($player)) {
                return $emptyProfile;
            }

            return [
                'available' => true,
                'profile_url' => $profileUrl,
                'labels' => array_values(array_map(static fn(array $badge): array => [
                    'label' => $badge['name'] ?? $badge['slug'] ?? '',
                    'text' => $badge['description'] ?? '',
                    'slug' => $badge['slug'] ?? null,
                    'type' => $badge['type'] ?? null,
                    'kind' => $badge['kind'] ?? null,
                    'source' => 'mobalytics',
                ], $player['badges']['items'] ?? [])),
                'ranks' => array_values($player['queuesStats']['items'] ?? []),
                'main_role_stats' => $player['roleStats']['defaultRole'] ?? null,
                'gpi' => $this->keyByType($player['gpi']['items'] ?? []),
                'performance_metrics' => array_values($player['performanceMetrics']['items'] ?? []),
            ];
        } catch (TransportExceptionInterface) {
            return $emptyProfile;
        }
    }

    private function getEmptyProfile(string $profileUrl): array
    {
        return [
            'available' => false,
            'profile_url' => $profileUrl,
            'labels' => [],
            'ranks' => [],
            'main_role_stats' => null,
            'gpi' => [],
            'performance_metrics' => [],
        ];
    }

    private function keyByType(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            if (isset($item['type'])) {
                $result[$item['type']] = $item['value'] ?? null;
            }
        }

        return $result;
    }

    private function normalizeServer(string $server): string
    {
        return match (strtolower($server)) {
            'eun1' => 'eune',
            'euw1' => 'euw',
            'na1' => 'na',
            'br1' => 'br',
            'jp1' => 'jp',
            'la1' => 'lan',
            'la2' => 'las',
            'oc1' => 'oce',
            'tr1' => 'tr',
            'me1' => 'mena',
            'sg2' => 'sea',
            'tw2' => 'tw',
            'vn2' => 'vn',
            default => strtolower($server),
        };
    }
}
