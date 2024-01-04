<?php

namespace App\Controller;

use App\ApiManager\LeagueApi;
use App\OpenAi\OpenAiApi;
use App\Repository\StatsRepository;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    public function __construct(
        private readonly StatsRepository $statsRepository,
        private readonly LeagueApi $leagueApi,
    )
    {
    }

    #[Route('/stats/my-winratio/{championId}', name: 'winratio')]
    public function winratio(string $championId): JsonResponse
    {
        return new JsonResponse([
            $this->statsRepository->getWinratioByChampion('H4oxX_PTSb6jYdnK5Nj6QwjIrPMzvAEIcK9LTtfawoOTb14', $championId),
        ]);
    }

    #[Route('/stats/all', name: 'stats-all')]
    public function statsAll(Request $request): JsonResponse
    {
        $data = $request->getSession()->get('data');

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);

        return new JsonResponse($this->statsRepository->getWinratioForAllChampions($summonerData['id']));
    }

    #[Route('/stats', name: 'stats-all', methods: ['POST'])]
    public function stats(Request $request): JsonResponse
    {
        $data = $request->getSession()->get('data');

        $filters = json_decode($request->getContent(), true);
        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);

        if ($filters['queueId'] === null && $filters['season'] === null) {
            return new JsonResponse($this->statsRepository->getWinratioForAllChampions($summonerData['id']));
        }

        return new JsonResponse(
            $this->statsRepository->getWinratioForAllChampions($summonerData['id'], $filters['queueId'], $filters['season'])
        );
    }

    #[Route('/stats/filters', name: 'stats-queues')]
    public function statsFilters(Request $request): JsonResponse
    {
        $data = $request->getSession()->get('data');

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);

        return new JsonResponse([
            'queues' => $this->statsRepository->getAvailableQueues($summonerData['id']),
            'seasons' => $this->statsRepository->getAvailableSeasons($summonerData['id']),
        ]);
    }

    #[Route('/stats/champion/{championId}/{queueId}/{position}/{season}', name: 'stats-champion')]
    public function statsChampion(Request $request, int $championId, int $queueId, string $position, int $season): JsonResponse
    {
        $data = $request->getSession()->get('data');

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);

        return new JsonResponse($this->statsRepository->getInfoAboutChampion($summonerData['id'], $queueId, $championId, $position, $season));
    }

     #[Route('/stats/by-queue/{queueId}', name: 'stats-by-queue')]
    public function statsByQueue(Request $request, int $queueId): JsonResponse
    {
        $data = $request->getSession()->get('data');

        $summonerData = $this->leagueApi->getSummonerDataByPuuid($data['puuid']);

        return new JsonResponse(
            $this->statsRepository->getWinratioForAllChampions($summonerData['id'], $queueId)
        );
    }

    #[Route('/test', name: 'test')]
    public function test(Request $request): JsonResponse
    {
        $data = $request->getSession()->get('data');


        return new JsonResponse(
            'xd'
        );
    }

}
