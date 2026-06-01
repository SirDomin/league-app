<?php

declare(strict_types=1);

namespace App\Controller\Analytics;

use App\Analyzer\PlayerAnalyzer;
use App\ApiManager\LeagueApi;
use App\Entity\Booster;
use App\Repository\BoosterRepository;
use App\Serializer\MatchSerializer;
use App\Utils\RegionMatcher;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoosterController extends AbstractController
{
    public function __construct(
        private readonly LeagueApi $leagueApi,
        private readonly EntityManagerInterface $entityManager,
        private readonly BoosterRepository $boosterRepository,
        private readonly PlayerAnalyzer $playerAnalyzer,
    )
    {
    }

    #[Route('/lolanal/healthcheck', name: 'healthcheck', methods: ['GET'])]
    public function lolanalHealthcheck(Request $request): Response
    {
        return new JsonResponse(['ok' => 'ok'], Response::HTTP_OK);
    }

    #[Route('/lolanal/register', name: 'register', methods: ['POST'])]
    public function lolanalRegister(Request $request): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $content = json_decode($request->getContent(), true);

        if (!isset($content['gameName']) || !$content['gameName']) {
            return new JsonResponse(['error' => 'gameName is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($content['gameTag']) || !$content['gameTag']) {
            return new JsonResponse(['error' => 'gameTag is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($content['region']) || !$content['region']) {
            return new JsonResponse(['error' => 'region is required'], Response::HTTP_BAD_REQUEST);
        }

        if (!RegionMatcher::isValidRegionOrPlatform($content['region'])) {
            return new JsonResponse(['error' => 'region ' . $content['region'] . ' is invalid'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $summonerData = $this->leagueApi->login($content['gameName'], $content['gameTag'], $content['region']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'User not found for region' . $content['region']], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($summonerData['puuid']) || !$summonerData['puuid']) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_BAD_REQUEST);
        }

        $data = $this->leagueApi->getSummonerDataByPuuid($summonerData['puuid'], server: $content['region']);

        if (!isset($data['profileIconId']) || !$data['profileIconId']) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_BAD_REQUEST);
        }

        /** @var Booster|null $existingBooster */
        $existingBooster = $this->boosterRepository->findOneBy([
            'puuid' => $data['puuid'],
        ]);

        if ($existingBooster !== null) {
            $existingBooster->setExpiresAt(new \DateTimeImmutable('now + 30 min'));
            $existingBooster->setRegion($content['region']);
            $existingBooster->setType(Booster::TYPE_BOOSTER);

            $this->entityManager->persist($existingBooster);
            $this->entityManager->flush();

            return new Response($serializer->serialize([
                'id' => $existingBooster->getId(),
                'gameName' => $existingBooster->getSummonerName(),
                'region' => $existingBooster->getRegion(),
                'tagLine' => $existingBooster->getSummonerTag(),
            ], 'json'), Response::HTTP_OK);
        }

        $booster = new Booster();
        $booster->setCreatedAt(new \DateTimeImmutable('now'));
        $booster->setRegion($content['region']);
        $booster->setPuuid($data['puuid']);
        $booster->setType(Booster::TYPE_BOOSTER);
        $booster->setIconId($data['profileIconId']);
        $booster->setSummonerName($data['gameName']);
        $booster->setSummonerTag($data['tagLine']);

        $this->entityManager->persist($booster);
        $this->entityManager->flush();

        return new Response($serializer->serialize([
            'id' => $booster->getId(),
            'gameName' => $booster->getSummonerName(),
            'tagLine' => $booster->getSummonerTag(),
            'region' => $booster->getRegion(),
        ], 'json'), Response::HTTP_CREATED);
    }

    #[Route('/lolanal/booster/{id}/set-icon-id', name: 'set-icon-id', methods: ['POST'])]
    public function lolanalSetIcon(int $id, Request $request): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $randomIconId = random_int(1, 28);

        /** @var Booster|null $existingBooster */
        $existingBooster = $this->boosterRepository->findOneBy(['id' => $id]);

        if ($existingBooster === null) {
            return new Response($serializer->serialize(['error' => 'User not found'], 'json'), Response::HTTP_NOT_FOUND);
        }

//        if ($existingBooster->isValid() === true) {
//            return new Response($serializer->serialize(['error' => 'User already verified'], 'json'), Response::HTTP_BAD_REQUEST);
//        }

        if ($existingBooster->getIconIdToVerify() !== null && $existingBooster->getIconIdToVerify() !== 0) {
            return new Response($serializer->serialize([
                'error' => 'already verifying',
                'icon' => 'https://ddragon.leagueoflegends.com/cdn/15.24.1/img/profileicon/' . $existingBooster->getIconIdToVerify() . '.png'
            ], 'json'), Response::HTTP_BAD_REQUEST);
        }

        $existingBooster->setIconIdToVerify($randomIconId);
        $existingBooster->setExpiresAt(new \DateTimeImmutable('now + 15 min'));

        $this->entityManager->persist($existingBooster);
        $this->entityManager->flush();

        return new Response($serializer->serialize([
            'icon' => 'https://ddragon.leagueoflegends.com/cdn/15.24.1/img/profileicon/' . $randomIconId . '.png',
        ], 'json'), Response::HTTP_UNAUTHORIZED);
    }

    #[Route('/lolanal/booster/{id}/verify', name: 'verify', methods: ['POST'])]
    public function lolanalVerify(int $id, Request $request): Response
    {
        $serializer = SerializerBuilder::create()->build();

        /** @var Booster|null $existingBooster */
        $existingBooster = $this->boosterRepository->findOneBy(['id' => $id]);

        if ($existingBooster === null) {
            return new Response($serializer->serialize(['error' => 'User not found'], 'json'), Response::HTTP_NOT_FOUND);
        }

        $now = new \DateTimeImmutable('now');

//        if ($existingBooster->getExpiresAt() < $now) {
//            $existingBooster->setExpiresAt(null);
//            $existingBooster->setIconIdToVerify(null);
//
//            $this->entityManager->persist($existingBooster);
//            $this->entityManager->flush();
//
//            return new Response($serializer->serialize([
//                'error' => 'User validation expired, use set-icon-id',
//                'date' => $existingBooster->getExpiresAt()?->format('Y-m-d H:i'),
//            ], 'json'), Response::HTTP_BAD_REQUEST);
//        }

        if ($existingBooster->getIconIdToVerify() === null) {
            return new Response($serializer->serialize([
                'error' => 'icon id not set, use /set-icon-id to set one',
            ], 'json'), Response::HTTP_BAD_REQUEST);
        }

        $data = $this->leagueApi->getSummonerDataByPuuid($existingBooster->getPuuid(), false, server: $existingBooster->getRegion());

        if ($data['profileIconId'] && $data['profileIconId'] === $existingBooster->getIconIdToVerify()) {
            $existingBooster->setIconIdToVerify(null);
            $existingBooster->setExpiresAt(null);
            $existingBooster->setValid(true);
            $existingBooster->setValidUntil(new \DateTimeImmutable('now + 1 month'));
        } else {
            $existingBooster->setIconIdToVerify(null);
            $existingBooster->setValid(false);
            $existingBooster->setValidUntil(null);
        }

        $this->entityManager->persist($existingBooster);
        $this->entityManager->flush();

        return new Response($serializer->serialize([
            'valid' => $existingBooster->isValid(),
            'debug' => [
              'current_icon' => $data['profileIconId'],
              'expected_icon' => $existingBooster->getIconIdToVerify(),
            ],
            'valid_until' => $existingBooster->getValidUntil()?->format('Y-m-d H:i:s') ?? null,
        ], 'json'), Response::HTTP_OK);
    }

    #[Route('/lolanal/booster/{id}/analyze', name: 'analyze', methods: ['POST'])]
    public function lolanalAnalyze(int $id, Request $request): Response
    {
        $serializer = SerializerBuilder::create()->build();

        /** @var Booster|null $existingBooster */
        $existingBooster = $this->boosterRepository->findOneBy(['id' => $id]);

        if ($existingBooster === null) {
            return new Response($serializer->serialize(['error' => 'User not found'], 'json'), Response::HTTP_NOT_FOUND);
        }

//        if ($existingBooster->isValid() !== true) {
//            return new Response($serializer->serialize([
//                'error' => 'User not verified',
//            ], 'json'), Response::HTTP_BAD_REQUEST);
//        }

//        if ($existingBooster->getValidUntil() !== null && $existingBooster->getValidUntil() < new \DateTimeImmutable('now')) {
//            return new Response($serializer->serialize([
//                'error' => 'User verification expired',
//            ], 'json'), Response::HTTP_BAD_REQUEST);
//        }

        $gamesToAnalyze = 20;

        return new Response($serializer->serialize([
            'analyze' => $this->playerAnalyzer->analyze(
                $existingBooster->getSummonerName(),
                $existingBooster->getSummonerTag(),
                $gamesToAnalyze,
                $existingBooster->getRegion()
            ),
            'games' => $gamesToAnalyze,
        ], 'json'), Response::HTTP_OK);
    }

    #[Route('/lolanal/player/{summonerName}/{gameTag}/{region}/{dateFrom}/{dateTo}/games', name: 'games', methods: ['GET'])]
    public function lolanalGames(string $summonerName, string $gameTag, string $region, \DateTime $dateFrom, \DateTime $dateTo): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $summoner = $this->leagueApi->login($summonerName, $gameTag, $region);

        if (!isset($summoner['puuid']) || !$summoner['puuid']) {
            return new Response($serializer->serialize(['error' => 'User not found'], 'json'), Response::HTTP_NOT_FOUND);
        }

        $existingPlayer = $this->boosterRepository->findOneBy(['puuid' => $summoner['puuid'], 'type' => Booster::TYPE_PLAYER]);

        if (!$existingPlayer) {
            $savedSummoner = new Booster();
            $savedSummoner->setType(Booster::TYPE_PLAYER);
            $savedSummoner->setPuuid($summoner['puuid']);
            $savedSummoner->setRegion($region);
            $savedSummoner->setSummonerName($summoner['gameName']);
            $savedSummoner->setSummonerTag($summoner['tagLine']);

            $this->entityManager->persist($savedSummoner);
            $this->entityManager->flush();

            /** @var Booster $existingPlayer */
            $existingPlayer = $this->boosterRepository->findOneBy(['puuid' => $summoner['puuid'], 'type' => Booster::TYPE_PLAYER]);
        }

        $matches = $this->leagueApi->getGamesHistoryByDate($existingPlayer->getPuuid(), $existingPlayer->getRegion(), $dateFrom, $dateTo, 10);

        unset($matches['cached']);

        $games = [];

        $rankData = $this->leagueApi->getSummonerLeaguesForServer($existingPlayer->getPuuid(), $region);

        unset($rankData['cached']);
        foreach ($matches as $id => $match) {
            $games[] =  MatchSerializer::serialize($this->leagueApi->getMatch($match, $region), $existingPlayer->getPuuid());
        }

        return new Response($serializer->serialize([
            'games' => $games,
            'rank' => self::normalizeRank($rankData),
        ], 'json'), Response::HTTP_OK);

    }

    private static function normalizeRank(?array $activePlayerRank): ?array
    {
        if (!$activePlayerRank || !is_array($activePlayerRank)) {
            return null;
        }

        $out = [];

        foreach ($activePlayerRank as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $queueType = $entry['queueType'] ?? null;
            if (!is_string($queueType)) {
                continue;
            }

            $normalized = [
                'tier'   => $entry['tier'] ?? null,                 // "MASTER"
                'rank'   => $entry['rank'] ?? null,                 // "I"
                'lp'     => isset($entry['leaguePoints']) ? (int)$entry['leaguePoints'] : null,
                'wins'   => isset($entry['wins']) ? (int)$entry['wins'] : null,
                'losses' => isset($entry['losses']) ? (int)$entry['losses'] : null,

                // opcjonalnie: możesz to też chcieć na FE
                'hotStreak'  => (bool)($entry['hotStreak'] ?? false),
                'freshBlood' => (bool)($entry['freshBlood'] ?? false),
                'inactive'   => (bool)($entry['inactive'] ?? false),
                'veteran'    => (bool)($entry['veteran'] ?? false),
            ];

            // mapowanie kolejek
            if ($queueType === 'RANKED_SOLO_5x5') {
                $out['solo'] = $normalized;
            } elseif ($queueType === 'RANKED_FLEX_SR') {
                $out['flex'] = $normalized;
            }
        }

        return $out ?: null;
    }

}
