<?php

namespace App\Controller;

use App\ApiManager\LeagueApi;
use App\Calculator\ScoreCalculator;
use App\DataScrapper\PorofessorScrapper;
use App\Entity\Clip;
use App\Entity\Game;
use App\Entity\Participant;
use App\Provider\FilterProvider;
use App\Provider\GameProvider;
use App\Provider\SummonerDataProvider;
use App\Repository\ClipRepository;
use App\Repository\GameRepository;
use App\Repository\ParticipantRepository;
use App\Utils\GameBackfiller;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

@ini_set("memory_limit",-1);

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
        private readonly GameBackfiller $gameBackfiller,
        private readonly ScoreCalculator $scoreCalculator,
        private readonly FilterProvider $filterProvider,
    ) { }

    #[Route('/game/active-data', name: 'game')]
    public function index(Request $request): Response
    {
        $data = $request->getSession()->get('data');

        $serializer = SerializerBuilder::create()->build();

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);
        $accountData = $this->leagueApi->getAccountData($data['puuid']);

        $data = $this->porofessorScrapper->getActiveData($summonerData['gameName'] . '-' . $accountData['tagLine']);

        return new Response($serializer->serialize(['data' => $data], 'json'));
    }

    #[Route('/game/by-puuid/{puuid}', name: 'game-show', methods: ['GET'])]
    public function show(string $puuid): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $game = $this->gameProvider->provideGameByMatchId($puuid);

        $game = $this->scoreCalculator->calculateScoreForGame($game);
        return new Response($serializer->serialize($game, 'json'));
    }

    #[Route('/game/active', name: 'game-find-active', methods: ['GET'])]
    public function findActive(Request $request): Response
    {
        $data = $request->getSession()->get('data');

        $serializer = SerializerBuilder::create()->build();

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);

        $game = $this->gameProvider->provideActiveGameForUser($summonerData['name'] ?? null, $summonerData['id']);

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
                    $accountData = $this->leagueApi->getAccountDataByRiotId($participant['gameName'], $participant['tagLine']);

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
                    $accountData = $this->leagueApi->getAccountDataByRiotId($participant['gameName'], $participant['tagLine']);

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
                    $accountData = $this->leagueApi->getAccountDataByRiotId($participant['gameName'], $participant['tagLine']);

                    $participantData = $this->leagueApi->getSummonerDataByPuuid($accountData['puuid']);
                }

                $participant['summonerId'] = $participantData['id'] ?? 0;
                $participant['puuid'] = $participantData['puuid'] ?? 0;

                $clientData['participants'][] = $participant;
            }
        }

        $game = $this->gameProvider->provideActiveGameForUser($summonerData['name'] ?? null, $summonerData['id'], $clientData);

        return new Response($serializer->serialize(['info' => $game], 'json'));
    }

    #[Route('/game/history/{limit}/{start}/{lastTimestamp}', name: 'game-get-history', methods: ['POST'])]
    public function getHistoryForUser(int $limit, int $start, int $lastTimestamp, Request $request): Response
    {
        $data = $request->getSession()->get('data');

        $serializer = SerializerBuilder::create()->build();

        $content = json_decode($request->getContent(), true);

        if (isset($content['filters'])) {
            $games = $this->gameProvider->getFilteredHistory($data['puuid'], $limit, $start, $lastTimestamp, $content['filters']);

        } else {
            $games = $this->gameProvider->getHistory($data['puuid'], $limit, $start, $lastTimestamp);
        }

        $filteredGames = array_map(function($game) use ($data) {
            if ($game === null) {
                return null;
            }
            $matchingParticipant = array_filter($game->getInfo()->getParticipants()->toArray(), function($participant) use ($data) {
                return $participant->getPuuid() === $data['puuid'];
            });

            if (empty($matchingParticipant)) {
                return null; // No matching participant found, handle as needed
            }
            $participant = reset($matchingParticipant);

            return [
                'id' => $game->getId(),
                'metadata' => [
                    'match_id' => $game->getMetadata()->getMatchId(),
                ],
                'info' => [
                    'game_creation' => $game->getInfo()->getGameCreation(),
                    'queue_id' => $game->getInfo()->getQueueId(),
                    'game_duration' => $game->getInfo()->getGameDuration(),
                    /** Participant $participant */
                    'participants' => [[
                        'puuid' => $participant->getPuuid(),
                        'id' => $participant->getId(),
                        'win' => $participant->getWin(),
                        'placement' => $participant->getPlacement(),
                        'summoner1_id' => $participant->getSummoner1Id(),
                        'summoner2_id' => $participant->getSummoner2Id(),
                        'item0' => $participant->getItem0(),
                        'item1' => $participant->getItem1(),
                        'item2' => $participant->getItem2(),
                        'item3' => $participant->getItem3(),
                        'item4' => $participant->getItem4(),
                        'item5' => $participant->getItem5(),
                        'item6' => $participant->getItem6(),
                        'kills' => $participant->getKills(),
                        'deaths' => $participant->getDeaths(),
                        'assists' => $participant->getAssists(),
                        'neutral_minions_killed' => $participant->getNeutralMinionsKilled(),
                        'gold_earned' => $participant->getGoldEarned(),
                        'total_minions_killed' => $participant->getTotalMinionsKilled(),
                        'champion_name' => $participant->getChampionName(),
                    ]],
                ],
            ];
        }, $games);

        $filteredGames = array_filter($filteredGames, function($game) {
            return $game !== null;
        });

        return new Response($serializer->serialize(['games' => $filteredGames], 'json'));
    }

    #[Route('/game/history/filters', name: 'game-get-history-filters', methods: ['GET'])]
    public function getHistoryFilters(Request $request): Response
    {
        $data = $request->getSession()->get('data');

        $serializer = SerializerBuilder::create()->build();

        return new Response($serializer->serialize($this->filterProvider->getAllFilters(), 'json'));
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

        $gameIds = $this->leagueApi->getGamesHistory($data['puuid'], 1, 0);

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

    #[Route('/test', name: 'test')]
    public function test(Request $request): Response
    {
        $data = $request->getSession()->get('data');

        $serializer = SerializerBuilder::create()->build();

        return new Response($serializer->serialize(['filters' => $this->filterProvider->getAllFilters()], 'json'));
    }

    #[Route('/test2', name: 'test2')]
    public function test2(Request $request): Response
    {

        $games = $this->gameRepository->getGamesToBackfill();

        $backfilled = 0;
        /** @var Game $game */
        foreach ($games as $game) {
            if ($this->gameBackfiller->backfillGame($game->getId())) {
                $backfilled ++;
            }
        }

        $game = $this->gameProvider->provideGameByMatchId('EUN1_3622285382');

        $serializer = SerializerBuilder::create()->build();

        dd('backfilled: ', $backfilled);
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
