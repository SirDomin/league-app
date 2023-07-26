<?php

namespace App\Controller;

use App\ApiManager\LeagueApi;
use App\DataScrapper\PorofessorScrapper;
use App\Entity\Participant;
use App\Provider\GameProvider;
use App\Provider\SummonerDataProvider;
use App\Repository\GameRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    public function __construct(
        private readonly GameProvider $gameProvider,
        private readonly LeagueApi $leagueApi,
        private readonly GameRepository $gameRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ParticipantRepository $participantRepository,
        private readonly PorofessorScrapper $porofessorScrapper
    ) { }

    #[Route('/game/active-data/{puuid}', name: 'game')]
    public function index(string $puuid): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($puuid);

        $data = $this->porofessorScrapper->getActiveData($summonerData['name']);

        return new Response($serializer->serialize(['data' => $data], 'json'));
    }

    #[Route('/game/by-puuid/{puuid}', name: 'game-show', methods: ['GET'])]
    public function show(string $puuid): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $game = $this->gameProvider->provideGameByMatchId($puuid);

        return new Response($serializer->serialize($game, 'json'));
    }

    #[Route('/game/active/{puuid}', name: 'game-find-active', methods: ['GET'])]
    public function findActive(string $puuid): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($puuid);

        $game = $this->gameProvider->provideActiveGameForUser($summonerData['name']);

        return new Response($serializer->serialize(['info' => $game], 'json'));
    }

    #[Route('/game/history/{puuid}/{limit}/{start}/{lastTimestamp}', name: 'game-get-history', methods: ['GET'])]
    public function getHistoryForUser(string $puuid, int $limit, int $start, int $lastTimestamp): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($puuid);

        $games = $this->gameProvider->getHistory($summonerData['name'], $limit, $start, $lastTimestamp);

        return new Response($serializer->serialize(['games' => $games], 'json'));
    }

    #[Route('/game/save/{matchId}', name: 'game-save-match-id', methods: ['GET'])]
    public function saveGameByMatchId(string $matchId): Response
    {
        $game = $this->gameRepository->findByMatchId($matchId);

        if ($game == null) {
            $game = $this->gameProvider->provideGameByMatchId($matchId);
            $this->entityManager->persist($game);
            $this->entityManager->flush();
        }

        $serializer = SerializerBuilder::create()->build();

        return new Response($serializer->serialize(
            [
                'game' => $game
            ],
            'json')
        );
    }

    #[Route('/game/save', name: 'game-save', methods: ['GET'])]
    public function saveGame(): Response
    {
        $gameIds = $this->leagueApi->getGamesHistory('SirDomin', 1, 0);

        $game = $this->gameProvider->provideGameByMatchId($gameIds[0]);

        $serializer = SerializerBuilder::create()->build();

        if($game->getId() === null) {
            $this->entityManager->persist($game);
            $this->entityManager->flush();
        }

        return new Response($serializer->serialize(
            [
                'game' => $game
            ],
            'json')
        );
    }

    #[Route('/game/last', name: 'game-get-last', methods: ['GET'])]
    public function getLast(): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $game = $this->gameRepository->getLastGame();

        return new Response($serializer->serialize(
            [
                'game' => $game
            ],
            'json')
        );
    }

    #[Route('/game/save-result', name: 'save-game-result', methods: ['POST'])]
    public function edit(Request $request): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $content = json_decode($request->getContent(), true);

        foreach ($content as $data) {
            /** @var Participant $summoner */
            $summoner = $this->participantRepository->findOneBy(['id' => (int) $data['id']]);

            $summoner->setComment($data['comment']);
            $this->entityManager->persist($summoner);
        }

        $this->entityManager->flush();

        return new Response($serializer->serialize(
            [
                'result' => 'ok'
            ],
            'json')
        );
    }
}
