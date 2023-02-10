<?php

namespace App\Controller;

use App\ApiManager\LeagueApi;
use App\Provider\GameProvider;
use App\Provider\SummonerDataProvider;
use App\Repository\GameRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    public function __construct(
        private readonly GameProvider $gameProvider,
        private readonly LeagueApi $leagueApi,
        private readonly GameRepository $gameRepository,
        private readonly EntityManagerInterface $entityManager,
    ) { }

    #[Route('/game', name: 'game')]
    public function index(): Response
    {
        $matchId = 'EUN1_3306952394';

        $game = $this->gameProvider->provideGameByMatchId($matchId);

        return new JsonResponse(['created' => $game]);
    }

    #[Route('/game/by-puuid/{puuid}', name: 'game-show', methods: ['GET'])]
    public function show(string $puuid): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $game = $this->gameProvider->provideGameByMatchId($puuid);

        return new Response($serializer->serialize($game, 'json'));
    }

    #[Route('/game/active/{summonerName}', name: 'game-find-active', methods: ['GET'])]
    public function findActive(string $summonerName): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $game = $this->gameProvider->provideActiveGameForUser($summonerName);

//        $g = "{\"info\":[{\"games_played\":1,\"summoner_id\":\"av5y7ed_g9eI_wx_Rl6C9bx5xfM2q8xzMvlhXQ5fOpqln4c\",\"summoner_name\":\"FakersMan\",\"team_id\":100,\"champion_id\":54},{\"games_played\":0,\"summoner_id\":\"s4HZXSmKodBRQ1BS5UQqJXQDDNy-Y2VN2X-ivy4kqC13JiE\",\"summoner_name\":\"WonderBoiTTT\",\"team_id\":100,\"champion_id\":80},{\"games_played\":0,\"summoner_id\":\"fWgPMFC8Y18AIAEbPfaXJnZC3n9jPNbuGsdxecFWMznYUyI\",\"summoner_name\":\"AADiN\",\"team_id\":100,\"champion_id\":51},{\"games_played\":0,\"summoner_id\":\"jaxFeP7I91zeKieYZmWKa3XdZ6Zx88CBrbD6Eo9EOrV1xpQ\",\"summoner_name\":\"Marcher\",\"team_id\":100,\"champion_id\":141},{\"games_played\":0,\"summoner_id\":\"G5Hj7bZvvJ6v6UBBdBRXG_mLhKrkszx6fHB7jcMHUl6eOgc\",\"summoner_name\":\"BornToBonk\",\"team_id\":100,\"champion_id\":90},{\"games_played\":0,\"summoner_id\":\"pr67AKdqhSGzOTGgsW_cRSt5EommNLzgBIWRhQkBR8_fwio\",\"summoner_name\":\"AngeLinHell\",\"team_id\":200,\"champion_id\":79},{\"games_played\":0,\"summoner_id\":\"23EegUJ-A3k20A_O2NlZ-TKOKbx7EvIQn_cytBxfap-4Yfk\",\"summoner_name\":\"Terror9611\",\"team_id\":200,\"champion_id\":74},{\"games_played\":0,\"summoner_id\":\"N7g5cen1pEHQejPVcvh8RfwjBeUAmBn99Fas0W4FwR-1GkU\",\"summoner_name\":\"Helloo\",\"team_id\":200,\"champion_id\":58},{\"games_played\":1018,\"summoner_id\":\"H4oxX_PTSb6jYdnK5Nj6QwjIrPMzvAEIcK9LTtfawoOTb14\",\"summoner_name\":\"SirDomin\",\"team_id\":200,\"champion_id\":121},{\"games_played\":215,\"summoner_id\":\"UrnLhXmr-nJPoW8sH9o4PuUhrwSrbmL3AR1VENKRLMvrG4M\",\"summoner_name\":\"MamSyndrąDowna\",\"team_id\":200,\"champion_id\":76}]}";
//        return new Response($g);
        return new Response($serializer->serialize(['info' => $game], 'json'));
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
}
