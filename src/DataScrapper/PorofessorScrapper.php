<?php

declare(strict_types=1);

namespace App\DataScrapper;

use Symfony\Component\DomCrawler\Crawler;

class PorofessorScrapper
{
    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36';

    public function __construct() {}

    public function getActiveData(string $summonerName, string $server = 'eune'): ?array
    {
        $server = $this->normalizeServer($server);

        $response = $this->request($this->buildPartialUrl($server, $summonerName), $server, $summonerName);

        if ($response === null) {
            return [];
        }

        ['body' => $responseBody, 'statusCode' => $statusCode] = $response;

        if (
            $statusCode >= 400
            || str_contains($responseBody, 'challenges.cloudflare.com')
            || str_contains($responseBody, 'Enable JavaScript and cookies to continue')
        ) {
            return [];
        }

        if (str_contains($responseBody, 'The summoner is not in-game, please retry later')) {
            return null;
        }

        $crawler = new Crawler();
        $crawler->addHtmlContent($responseBody);

        $members = $crawler->filter('.card-5')->each(function (Crawler $node) {
            $body = $this->firstNode($node, '.cardBody') ?? $node;
            $championBox = $this->firstNode($body, '.championBox') ?? $body;
            $tagsArray = $body->filter('.tags-box .tag')->each(function (Crawler $tag) {
                return trim($tag->text(''));
            });

            return [
                'premade' => $this->firstText($node, '.premadeHistoryTagContainer'),
                'nickname' => $this->getNickname($node),
                'wr' => $this->firstText($championBox, '.txt .title'),
                'rank' => $this->firstText($championBox, '.rankingExternalLink'),
                'tags' => array_values(array_filter($tagsArray)),
                'source' => 'porofessor',
                'summoner_id' => $node->attr('data-summonerid'),
                'team' => $this->getTeam($node),
                'profile_url' => $this->firstAttr($node, '.cardHeader a', 'href'),
                'champion' => $this->firstAttr($championBox, '.imgColumn-champion img', 'alt'),
                'summoner_level' => $this->getInteger($this->firstText($championBox, '.level')),
                'spells' => $championBox->filter('.spells img[alt]')->each(function (Crawler $spell) {
                    return trim((string) $spell->attr('alt'));
                }),
                'champion_stats' => [
                    'kills' => $this->getFloat($this->firstText($championBox, '.kills')),
                    'deaths' => $this->getFloat($this->firstText($championBox, '.deaths')),
                    'assists' => $this->getFloat($this->firstText($championBox, '.assists')),
                ],
                'mastery' => $this->firstAttr($championBox, '.championMasteryLevelIcon', 'alt'),
                'solo_rank' => $this->firstAttr($body, '.rankingsBox img[alt]', 'alt'),
                'main_role' => $this->firstAttr($body, '.rolesBox img[alt]', 'alt'),
            ];
        });

        return $members;
    }

    private function request(string $url, string $server, string $summonerName): ?array
    {
        $ch = curl_init();

        if ($ch === false) {
            return null;
        }

        $cookieFile = tempnam(sys_get_temp_dir(), 'porofessor_');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');

        if (is_string($cookieFile)) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        }

        $headers = [];
        $headers[] = 'Accept: text/html, */*; q=0.01';
        $headers[] = 'Accept-Language: pl-PL,pl;q=0.9,en-US;q=0.8,en;q=0.7';
        $headers[] = 'Cache-Control: no-cache';
        $headers[] = 'Connection: keep-alive';
        $headers[] = \sprintf('Cookie: searchRegion=%s; languageBanner_pl_count=1', $server);
        $headers[] = \sprintf('Referer: https://porofessor.gg/live/%s/%s', $server, rawurlencode($summonerName));
        $headers[] = 'Pragma: no-cache';
        $headers[] = 'Sec-Fetch-Dest: empty';
        $headers[] = 'Sec-Fetch-Mode: cors';
        $headers[] = 'Sec-Fetch-Site: same-origin';
        $headers[] = 'User-Agent: ' . self::USER_AGENT;
        $headers[] = 'X-Requested-With: XMLHttpRequest';
        $headers[] = 'sec-ch-ua: "Google Chrome";v="125", "Chromium";v="125", "Not.A/Brand";v="24"';
        $headers[] = 'sec-ch-ua-mobile: ?0';
        $headers[] = 'sec-ch-ua-platform: "Windows"';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (is_string($cookieFile) && file_exists($cookieFile)) {
            unlink($cookieFile);
        }

        if (!is_string($response)) {
            return null;
        }

        return [
            'body' => $response,
            'statusCode' => $statusCode,
        ];
    }

    private function buildPartialUrl(string $server, string $summonerName): string
    {
        return \sprintf('https://porofessor.gg/partial/live-partial/%s/%s', $server, rawurlencode($summonerName));
    }

    private function getNickname(Crawler $node): string
    {
        $nickname = $this->firstText($node, '.cardHeader a');

        if ($nickname !== '') {
            return $nickname;
        }

        return trim((string) $node->attr('data-summonername'));
    }

    private function normalizeServer(string $server): string
    {
        return match (strtolower($server)) {
            'eun1' => 'eune',
            'euw1' => 'euw',
            'na1' => 'na',
            'kr' => 'kr',
            default => strtolower($server),
        };
    }

    private function getTeam(Crawler $node): ?string
    {
        $header = $this->firstNode($node, '.cardHeader');

        if ($header === null) {
            return null;
        }

        if ($header->matches('.blue')) {
            return 'blue';
        }

        if ($header->matches('.red')) {
            return 'red';
        }

        return null;
    }

    private function getInteger(string $value): ?int
    {
        $value = preg_replace('/[^0-9-]/', '', $value);

        return $value !== '' ? (int) $value : null;
    }

    private function getFloat(string $value): ?float
    {
        $value = str_replace(',', '.', trim($value));

        return is_numeric($value) ? (float) $value : null;
    }

    private function firstNode(Crawler $node, string $selector): ?Crawler
    {
        $filtered = $node->filter($selector);

        if ($filtered->count() === 0) {
            return null;
        }

        return $filtered->first();
    }

    private function firstText(Crawler $node, string $selector): string
    {
        $firstNode = $this->firstNode($node, $selector);

        if ($firstNode === null) {
            return '';
        }

        return trim($firstNode->text(''));
    }

    private function firstAttr(Crawler $node, string $selector, string $attribute): ?string
    {
        $firstNode = $this->firstNode($node, $selector);

        if ($firstNode === null) {
            return null;
        }

        $value = $firstNode->attr($attribute);

        return $value !== null ? trim($value) : null;
    }
}
