<?php

declare(strict_types=1);

namespace App\DataScrapper;

use Symfony\Component\DomCrawler\Crawler;

class PorofessorScrapper
{
    public function __construct() {}

    public function getActiveData(string $summonerName): ?array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, \sprintf('https://porofessor.gg/partial/live-partial/eune/%s', $summonerName));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

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
        curl_close($ch);

        if(str_contains($response, 'The summoner is not in-game, please retry later')) {
            return null;
        }

        $crawler = new Crawler();

        $content = $response;

        $crawler->addHtmlContent($content);

        $members =  $crawler->filter('.card-5')->each(function (Crawler $node, $i) {
            $body = $node->filter('.cardBody');
            $tags = $body->evaluate('//div[@class="box tags-box "]')->eq($i);
            $tagsCrawler = new Crawler($tags->html());
            $tagsArray = $tagsCrawler->filter('div')->each(function (Crawler $node, $i) {
                return $node->text();
            });

             return [
                 'premade' => $node->filter('.premadeHistoryTagContainer')->text(),
                 'nickname' => $node->filter('a')->first()->text(),
                 'wr' => $body->filter('.txt')->first()->filter('.title')->first()->text(),
                 'rank' => $body->filter('.content')->filter('a')->first()->text(),
                 'tags' => $tagsArray,
             ];
        });

        return $members;
    }
}
