<?php

namespace App\Controller;

use App\ApiManager\LeagueApi;
use App\Entity\Game;
use App\Entity\Metadata;
use App\Repository\GameRepository;
use App\Transformer\InfoTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameController extends AbstractController
{
    public function __construct(
        private readonly LeagueApi $leagueApi,
        private readonly EntityManagerInterface $entityManager,
        private readonly GameRepository $gameRepository,
    )
    {

    }

    #[Route('/game', name: 'game')]
    public function index(): Response
    {
        $matchId = 'EUN1_3305598197';

        $game = $this->gameRepository->findByMatchId($matchId);

        if ($game) {
            dd($game);
        }

        $game = $this->leagueApi->getGameById('EUN1_3305598197');

        $metadata = new Metadata();
        $metadata->setMatchId($game['metadata']['matchId']);
        $metadata->setDataVersion($game['metadata']['dataVersion']);
        $metadata->setParticipants($game['metadata']['participants']);
        $info = InfoTransformer::getInfo($game['info']);

        $game = new Game();
        $game->setInfo($info);
        $game->setMetadata($metadata);

        $this->entityManager->persist($game);
        $this->entityManager->flush();

        return new JsonResponse(['created' => $game]);
    }

    #[Route('/game/{id}', name: 'game-show')]
    public function show(int $id): JsonResponse
    {
//        $participant = $this->
//        $game = $this->leagueApi->getGameById('EUN1_3305598197');
//
//        $participant = ParticipantTransformer::getParticipant($game['info']['participants'][0]);
//
//        $this->entityManager->persist($participant);
//        $this->entityManager->flush();
//
//        return new JsonResponse($participant);
    }
}
