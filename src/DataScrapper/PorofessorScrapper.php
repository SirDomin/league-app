<?php

declare(strict_types=1);

namespace App\DataScrapper;

use Symfony\Component\DomCrawler\Crawler;

class PorofessorScrapper
{
    public function __construct() {}

    public function getActiveData(string $summonerName, string $server = 'eune'): ?array
    {
        $server = $this->normalizeServer($server);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, \sprintf('https://porofessor.gg/live/%s/%s', $server, rawurlencode($summonerName)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $headers = array();
        $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7';
        $headers[] = 'Accept-Language: pl,en;q=0.9,en-GB;q=0.8,en-US;q=0.7,mt;q=0.6';
        $headers[] = 'Cache-Control: max-age=0';
        $headers[] = 'Connection: keep-alive';
        $headers[] = 'Cookie: searchRegion=eune; languageBanner_pl_count=6';
        $headers[] = 'Sec-Fetch-Dest: document';
        $headers[] = 'Sec-Fetch-Mode: navigate';
        $headers[] = 'Sec-Fetch-Site: none';
        $headers[] = 'Sec-Fetch-User: ?1';
        $headers[] = 'Upgrade-Insecure-Requests: 1';
        $headers[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36 Edg/112.0.1722.48';
        $headers[] = 'sec-ch-ua: "Chromium";v="112", "Microsoft Edge";v="112", "Not:A-Brand";v="99"';
        $headers[] = 'sec-ch-ua-mobile: ?0';
        $headers[] = 'sec-ch-ua-platform: "Windows"';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (
            !is_string($response)
            || $statusCode >= 400
            || str_contains($response, 'challenges.cloudflare.com')
            || str_contains($response, 'Enable JavaScript and cookies to continue')
        ) {
            return [];
        }

        if(str_contains($response, 'The summoner is not in-game, please retry later')) {
            return null;
        }

        $crawler = new Crawler();

        $content = $response;

        $crawler->addHtmlContent($content);

        $members = $crawler->filter('.card-5')->each(function (Crawler $node) {
            $body = $node->filter('.cardBody');
            $championBox = $body->filter('.championBox')->first();
            $tagsArray = $body->filter('.tags-box .tag')->each(function (Crawler $tag) {
                return trim($tag->text(''));
            });

            return [
                'premade' => trim($node->filter('.premadeHistoryTagContainer')->text('')),
                'nickname' => $this->getNickname($node),
                'wr' => trim($championBox->filter('.txt .title')->first()->text('')),
                'rank' => trim($championBox->filter('.rankingExternalLink')->first()->text('')),
                'tags' => array_values(array_filter($tagsArray)),
                'source' => 'porofessor',
                'summoner_id' => $node->attr('data-summonerid'),
                'team' => $this->getTeam($node),
                'profile_url' => $node->filter('.cardHeader a')->first()->attr('href'),
                'champion' => $championBox->filter('.imgColumn-champion img')->first()->attr('alt'),
                'summoner_level' => $this->getInteger($championBox->filter('.level')->first()->text('')),
                'spells' => $championBox->filter('.spells img[alt]')->each(function (Crawler $spell) {
                    return trim((string) $spell->attr('alt'));
                }),
                'champion_stats' => [
                    'kills' => $this->getFloat($championBox->filter('.kills')->first()->text('')),
                    'deaths' => $this->getFloat($championBox->filter('.deaths')->first()->text('')),
                    'assists' => $this->getFloat($championBox->filter('.assists')->first()->text('')),
                ],
                'mastery' => $championBox->filter('.championMasteryLevelIcon')->first()->attr('alt'),
                'solo_rank' => $body->filter('.rankingsBox img[alt]')->first()->attr('alt'),
                'main_role' => $body->filter('.rolesBox img[alt]')->first()->attr('alt'),
            ];
        });

        return $members;
    }

    private function getNickname(Crawler $node): string
    {
        $nickname = trim($node->filter('a')->first()->text(''));

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
        $header = $node->filter('.cardHeader')->first();

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
}
