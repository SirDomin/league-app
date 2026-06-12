<?php

namespace App\Controller;

use App\Analyzer\PlayerAnalyzer;
use App\ApiManager\LeagueApi;
use App\Calculator\ScoreCalculator;
use App\Entity\Game;
use App\Entity\Participant;
use App\Exception\ApiRateExceededException;
use App\Provider\GameProvider;
use App\Provider\SummonerDataProvider;
use App\Repository\GameRepository;
use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

@ini_set("memory_limit",-1);

class SummonerController extends AbstractController
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly SummonerDataProvider $summonerDataProvider,
        private readonly GameProvider $gameProvider,
        private readonly ParticipantRepository $participantRepository,
        private readonly LeagueApi $leagueApi,
        private readonly ScoreCalculator $scoreCalculator,
        private readonly PlayerAnalyzer $playerAnalyzer,
    ){ }

    #[Route('/summoner/{summonerId}', name: 'summoner-show', methods: ['GET'])]
    public function show(string $summonerId): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $games = $this->gameRepository->getAllGamesWithPlayerBySummonerId($summonerId);

        return new Response($serializer->serialize($games, 'json'));
    }

    #[Route('/summoners/championSelect', name: 'champion-select', methods: ['POST'])]
    public function showChampionSelect(Request $request): Response
    {
//        $serializer = SerializerBuilder::create()->build();
//
//        $content = json_decode($request->getContent(), true);
//
//        $participantsData = [];
//
//        foreach ($content as $data) {
//            $summonerData = $this->summonerDataProvider->getDataByName($data['gameName'], $data['gameTag']);
//
//            $participantsData[] = [
//                  'summonerId' => $summonerData['id'],
//                  'summonerName' => $summonerData['gameName'],
//                  'puuid' => $summonerData['puuid'],
//                  'teamId' => null,
//                  'championId' => null,
//                  'clientId' => $data['clientId'],
//              ];
//        }
//
//        return new Response($serializer->serialize(['info' => $this->gameProvider->connectParticipants(['participants' => $participantsData], null)], 'json'));
    }

    #[Route('/summoners/find-by-name/{playerName}', name: 'find-by-name', methods: ['GET'])]
    public function findByName(Request $request, string $playerName): Response
    {
        dd('xd');
//        $serializer = SerializerBuilder::create()->build();
//
//        $content = json_decode($request->getContent(), true);
//
//        $participantsData = [];
//
////        if (strlen($playerName) < 3) {
////            return new Response($serializer->serialize(['info' => $this->gameProvider->connectParticipants(['participants' => $participantsData], null)], 'json'));
////        }
//
//        $participants = $this->participantRepository
//            ->createQueryBuilder('p')
//            ->select('p.puuid,  p.summonerId')
//            ->where('p.summonerName LIKE :name')
//            ->orWhere('p.riotIdGameName LIKE :name')
//            ->setParameter('name', '%' . $playerName . '%')
//            ->groupBy('p.puuid', 'p.summonerId')
//            ->getQuery()
//            ->getResult()
//        ;
//
//        foreach ($participants as $participant) {
//            $participant['teamId'] = null;
//            $participant['championId'] = null;
//
//            $participantsData[] = $participant;
//        }
//
//        return new Response($serializer->serialize(['info' => $this->gameProvider->connectParticipants(['participants' => $participantsData], null)], 'json'));
    }

    #[Route('/summoners/get-by-name/{playerName}', name: 'get-by-name', methods: ['GET'])]
    public function getAllUsersWithName(Request $request, string $playerName): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $participants = $this->participantRepository
            ->createQueryBuilder('p')
            ->select('p.puuid,  p.summonerId')
            ->where('p.summonerName LIKE :name')
            ->orWhere('p.riotIdGameName LIKE :name')
            ->setParameter('name', '%' . $playerName . '%')
            ->groupBy('p.puuid', 'p.summonerId')
            ->getQuery()
            ->getResult()
        ;

        $participants = array_map(function(array $participantData): array {
            return $this->summonerDataProvider->getDataForParticipant($participantData['puuid']);
        }, $participants);

        return new Response($serializer->serialize($participants, 'json'));
    }

    #[Route('/summoner/{summonerName}/count', name: 'summoner-count', methods: ['GET'])]
    public function count(string $summonerName): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $summonerUuid = $this->summonerDataProvider->getPuuidByName($summonerName);

        return new Response($serializer->serialize(['games' => $this->gameRepository->countAllGamesWithPlayer($summonerUuid)], 'json'));
    }

    #[Route('/summoner/{summonerName}/{tag}/count', name: 'summoner-tag-count', methods: ['GET'])]
    public function countWithNameAndTag(string $summonerName, string $tag, Request $request): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $accountData = $this->leagueApi->getAccountDataByRiotId($summonerName, $tag);
        $activePuuid = $this->getAuthenticatedPuuid($request);

        return new Response($serializer->serialize([
            'games' => $this->gameRepository->countAllGamesWithPlayer($accountData['puuid'], $activePuuid)
        ], 'json'));
    }

    #[Route('/summoner/{summonerName}/{tag}/analyze', name: 'summoner-analyze', methods: ['GET'])]
    public function analyzePlayer(string $summonerName, string $tag): Response
    {
        $serializer = SerializerBuilder::create()->build();

        try {
            $playerData = $this->playerAnalyzer->analyze($summonerName, $tag);

        } catch (ApiRateExceededException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 429);
        }

        return new Response($serializer->serialize($playerData, 'json'));
    }

    #[Route('/summoner/{summonerName}/{tag}/active', name: 'summoner-game-active', methods: ['GET'])]
    public function getActiveGameForSummoner(string $summonerName, string $tag): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $accountData = $this->leagueApi->getAccountDataByRiotId($summonerName, $tag);

        return new Response($serializer->serialize($this->leagueApi->getCurrentGameForUser($accountData['puuid']), 'json'));
    }

    #[Route('/summoner/{summonerName}/{tag}/games', name: 'summoner-tag-games', methods: ['GET'])]
    public function getGamesWithNameAndTag(string $summonerName, string $tag, Request $request): Response
    {
        $serializer = SerializerBuilder::create()->build();
        $limit = max(1, min(50, $request->query->getInt('limit', 20)));
        $start = max(0, $request->query->getInt('start', 0));
        $activePuuid = $this->getAuthenticatedPuuid($request);

        $accountData = $this->leagueApi->getAccountDataByRiotId($summonerName, $tag);

        $games = $this->gameRepository->getAllGamesWithPlayer($accountData['puuid'], $limit + 1, $start, $activePuuid);
        $hasMore = count($games) > $limit;
        $games = array_slice($games, 0, $limit);

        $fullDataGames = [];

        foreach ($games as $game) {
            $fullGameData = $this->scoreCalculator->calculateScoreForGame($this->gameRepository->find($game['id']));

            $participants = $fullGameData->getInfo()->getParticipants();
            $targetParticipant = $this->getParticipantByPuuid($participants, $accountData['puuid']);
            $teamRelation = null;
            $activePlayerWin = null;

            if ($activePuuid) {
                $activeParticipant = $this->findParticipantByPuuid($participants, $activePuuid);
                if ($activeParticipant === null) {
                    continue;
                }

                $teamRelation = $this->areParticipantsAllies($targetParticipant, $activeParticipant)
                    ? 'Ally'
                    : 'Enemy';
                $activePlayerWin = $activeParticipant->getWin();
                $targetParticipant->setTeamRelation($teamRelation);
                $targetParticipant->setActivePlayerWin($activePlayerWin);
            }

            $fullGameData->getInfo()->setParticipants([
                $targetParticipant
            ]);

            $fullDataGames[] = $this->serializeGameWithParticipantContext(
                $serializer,
                $fullGameData,
                $teamRelation,
                $activePlayerWin
            );
        }

        return new Response($serializer->serialize([
            'games' => $fullDataGames,
            'limit' => $limit,
            'start' => $start,
            'nextStart' => $start + count($fullDataGames),
            'hasMore' => $hasMore,
        ], 'json'));
    }

    private function getParticipantByPuuid(Collection $participants, string $puuid): Participant
    {
        $participant = $this->findParticipantByPuuid($participants, $puuid);
        if ($participant !== null) {
            return $participant;
        }

        throw new \Exception('user not found in game');
    }

    private function findParticipantByPuuid(Collection $participants, string $puuid): ?Participant
    {
        foreach ($participants as $participant) {
            if ($participant->getPuuid() === $puuid) {
                return $participant;
            }
        }

        return null;
    }

    private function getAuthenticatedPuuid(Request $request): ?string
    {
        $data = $request->getSession()->get('data');

        return is_array($data) && !empty($data['puuid']) ? $data['puuid'] : null;
    }

    private function serializeGameWithParticipantContext(
        $serializer,
        Game $game,
        ?string $teamRelation,
        ?bool $activePlayerWin
    ): array {
        $serializedGame = json_decode($serializer->serialize($game, 'json'), true);
        if (!is_array($serializedGame)) {
            return [];
        }

        if (isset($serializedGame['info']['participants'][0])
            && is_array($serializedGame['info']['participants'][0])
        ) {
            $participant = &$serializedGame['info']['participants'][0];
            $participant['team_relation'] = $teamRelation;
            $participant['teamRelation'] = $teamRelation;
            $participant['active_player_win'] = $activePlayerWin;
            $participant['activePlayerWin'] = $activePlayerWin;
        }

        $serializedGame['player_context'] = [
            'team_relation' => $teamRelation,
            'teamRelation' => $teamRelation,
            'active_player_win' => $activePlayerWin,
            'activePlayerWin' => $activePlayerWin,
        ];
        $serializedGame['playerContext'] = $serializedGame['player_context'];

        return $serializedGame;
    }

    private function areParticipantsAllies(Participant $targetParticipant, Participant $activeParticipant): bool
    {
        $targetSubteamId = $targetParticipant->getPlayerSubteamId();
        $activeSubteamId = $activeParticipant->getPlayerSubteamId();

        if ($targetSubteamId !== null && $targetSubteamId > 0 && $activeSubteamId !== null && $activeSubteamId > 0) {
            return $targetSubteamId === $activeSubteamId;
        }

        return $targetParticipant->getTeamId() === $activeParticipant->getTeamId();
    }
}
