<?php

namespace App\Controller;

use App\ApiManager\LeagueApi;
use App\DataScrapper\PorofessorScrapper;
use App\Entity\Clip;
use App\Entity\Game;
use App\Entity\Participant;
use App\Provider\GameProvider;
use App\Provider\SummonerDataProvider;
use App\Repository\ClipRepository;
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
        private readonly PorofessorScrapper $porofessorScrapper,
        private readonly ClipRepository $clipRepository,
    ) { }

    #[Route('/game/active-data', name: 'game')]
    public function index(Request $request): Response
    {
        $data = $request->getSession()->get('data');

        $serializer = SerializerBuilder::create()->build();

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);
        $accountData = $this->leagueApi->getAccountData($data['puuid']);

        $data = $this->porofessorScrapper->getActiveData($summonerData['name'] . '-' . $accountData['tagLine']);

        return new Response($serializer->serialize(['data' => $data], 'json'));
    }

    #[Route('/game/by-puuid/{puuid}', name: 'game-show', methods: ['GET'])]
    public function show(string $puuid): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $game = $this->gameProvider->provideGameByMatchId($puuid);

        return new Response($serializer->serialize($game, 'json'));
    }

    #[Route('/game/active', name: 'game-find-active', methods: ['GET'])]
    public function findActive(Request $request): Response
    {
        $data = $request->getSession()->get('data');

        $serializer = SerializerBuilder::create()->build();

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);

        $game = $this->gameProvider->provideActiveGameForUser($summonerData['name'], $summonerData['id']);

        return new Response($serializer->serialize(['info' => $game], 'json'));
    }

    #[Route('/game/active-client', name: 'game-find-active-client', methods: ['POST'])]
    public function findActiveClient(Request $request): Response
    {
        $data = $request->getSession()->get('data');

        $serializer = SerializerBuilder::create()->build();

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);

        $content = json_decode($request->getContent(), true);

        $clientData = [
            'participants' => [],
        ];

        $gameMode = $content['gameData']['queue']['id'];


        if ($gameMode === 1700) {
            foreach ($content['gameData']['teamOne'] as $participant) {
                $participant['teamId'] = 100;
                $participantData = $this->leagueApi->getSummonerData($participant['summonerName']);

                if ($participantData === []) {
                    $accountData = $this->leagueApi->getAccountDataByRiotId($participant['data']['gameName'], $participant['data']['tagLine']);

                    $participantData = $this->leagueApi->getSummonerDataByPuuid($accountData['puuid']);
                }

                $participant['summonerId'] = $participantData['id'];
                $participant['puuid'] = $participantData['puuid'];

                $clientData['participants'][] = $participant;
            }
        } else {
            foreach ($content['gameData']['teamOne'] as $participant) {
                $participant['teamId'] = 100;
                $participantData = $this->leagueApi->getSummonerData($participant['summonerName']);

                if ($participantData === []) {
                    $accountData = $this->leagueApi->getAccountDataByRiotId($participant['data']['gameName'], $participant['data']['tagLine']);

                    $participantData = $this->leagueApi->getSummonerDataByPuuid($accountData['puuid']);
                }

                $participant['summonerId'] = $participantData['id'] ?? 0;
                $participant['puuid'] = $participantData['puuid'] ?? 0;

                $clientData['participants'][] = $participant;
            }
            foreach ($content['gameData']['teamTwo'] as $participant) {
                $participant['teamId'] = 200;
                $participantData = $this->leagueApi->getSummonerData($participant['summonerName']);

                if ($participantData === []) {
                    $accountData = $this->leagueApi->getAccountDataByRiotId($participant['data']['gameName'], $participant['data']['tagLine']);

                    $participantData = $this->leagueApi->getSummonerDataByPuuid($accountData['puuid']);
                }

                $participant['summonerId'] = $participantData['id'] ?? 0;
                $participant['puuid'] = $participantData['puuid'] ?? 0;

                $clientData['participants'][] = $participant;
            }
        }

        $game = $this->gameProvider->provideActiveGameForUser($summonerData['name'], $summonerData['id'], $clientData);

        return new Response($serializer->serialize(['info' => $game], 'json'));
    }

    #[Route('/game/history/{limit}/{start}/{lastTimestamp}', name: 'game-get-history', methods: ['GET'])]
    public function getHistoryForUser(int $limit, int $start, int $lastTimestamp, Request $request): Response
    {
        $data = $request->getSession()->get('data');

        $serializer = SerializerBuilder::create()->build();

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);

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

    #[Route('/game/{matchId}/timeline', name: 'game-game-timeline', methods: ['GET'])]
    public function getGameTimeline(string $matchId): Response
    {
        $timeline = $this->leagueApi->getTimelineForMatchId($matchId);

        $serializer = SerializerBuilder::create()->build();

        return new Response($serializer->serialize(
            [
                'game' => $timeline
            ],
            'json')
        );
    }

    #[Route('/game/save', name: 'game-save', methods: ['GET'])]
    public function saveGame(Request $request): Response
    {
        $data = $request->getSession()->get('data');
        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);

        $gameIds = $this->leagueApi->getGamesHistory($summonerData['name'], 1, 0);

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

    #[Route('/game/byId/{id}', name: 'game-get-by-id', methods: ['GET'])]
    public function getById(int $id): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $game = $this->gameRepository->find($id);

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

        foreach ($content['participants'] as $data) {
            /** @var Participant $summoner */
            $summoner = $this->participantRepository->findOneBy(['id' => (int) $data['id']]);

            $summoner->setComment($data['comment']);
            $this->entityManager->persist($summoner);
        }

        /** @var Game|null $game */
        $game = $this->gameRepository->getGameByInfoId($content['infoId']);

        foreach ($content['clips'] as $clip) {
            if ($clip['id'] !== null) {
                $clipEntity = $this->clipRepository->find((int) $clip['id']);
            } else {
                $clipEntity = new Clip();
            }
            $clipEntity->setUrl($clip['url']);
            $clipEntity->setTitle($clip['title']);
            $clipEntity->setInfo($game->getInfo());

            $this->entityManager->persist($clipEntity);
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
