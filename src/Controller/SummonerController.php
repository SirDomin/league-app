<?php

namespace App\Controller;

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

class SummonerController extends AbstractController
{
    public function __construct(
        private readonly GameRepository $gameRepository,
        private readonly SummonerDataProvider $summonerDataProvider,
        private readonly ParticipantRepository $participantRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly GameProvider $gameProvider,
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
        $serializer = SerializerBuilder::create()->build();

        $content = json_decode($request->getContent(), true);

        $participantsData = [];

        foreach ($content as $data) {
            $summonerData = $this->summonerDataProvider->getDataByName($data['summonerName']);

            $participantsData[] = [
                  'summonerId' => $summonerData['id'],
                  'summonerName' => $summonerData['name'],
                  'teamId' => null,
                  'championId' => null,
              ];
        }

        return new Response($serializer->serialize(['info' => $this->gameProvider->connectParticipants(['participants' => $participantsData])], 'json'));
    }

    #[Route('/summoner/{summonerName}/count', name: 'summoner-count', methods: ['GET'])]
    public function count(string $summonerName): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $summonerUuid = $this->summonerDataProvider->getPuuidByName($summonerName);

        return new Response($serializer->serialize(['games' => $this->gameRepository->countAllGamesWithPlayer($summonerUuid)], 'json'));
    }

    #[Route('/game/save-result', name: 'summoner-update', methods: ['POST'])]
    public function edit(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);

        foreach ($content as $data) {
            /** @var Participant $summoner */
            $summoner = $this->participantRepository->findOneBy(['id' => (int) $data['id']]);

            $summoner->setComment($data['comment']);
            $this->entityManager->persist($summoner);
        }

        $this->entityManager->flush();

        return new Response();
    }
}
