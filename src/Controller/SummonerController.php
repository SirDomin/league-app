<?php

namespace App\Controller;

use App\Provider\SummonerDataProvider;
use App\Repository\GameRepository;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SummonerController extends AbstractController
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly SummonerDataProvider $summonerDataProvider,
    ){ }

    #[Route('/summoner/{summonerName}', name: 'summoner-show', methods: ['GET'])]
    public function show(string $summonerName): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $summonerUuid = $this->summonerDataProvider->getPuuidByName($summonerName);
        $games = $this->gameRepository->getAllGamesWithPlayer($summonerUuid);

        return new Response($serializer->serialize($games, 'json'));
    }

    #[Route('/summoner/{summonerName}/count', name: 'summoner-count', methods: ['GET'])]
    public function count(string $summonerName): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $summonerUuid = $this->summonerDataProvider->getPuuidByName($summonerName);

        return new Response($serializer->serialize(['games' => $this->gameRepository->countAllGamesWithPlayer($summonerUuid)], 'json'));
    }
}
