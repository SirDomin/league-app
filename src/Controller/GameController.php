<?php

namespace App\Controller;

use App\Provider\GameProvider;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    public function __construct(
        private readonly GameProvider $gameProvider,
    ) { }

    #[Route('/game', name: 'game')]
    public function index(): Response
    {
        $matchId = 'EUN1_3306952394';

        $game = $this->gameProvider->provideGameByMatchId($matchId);

        return new JsonResponse(['created' => $game]);
    }

    #[Route('/game/{puuid}', name: 'game-show', methods: ['GET'])]
    public function show(string $puuid): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $game = $this->gameProvider->provideGameByMatchId($puuid);

        return new Response($serializer->serialize($game, 'json'));
    }
}
