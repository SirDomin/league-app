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
        private readonly GameProvider $gameProvider,
        private readonly ParticipantRepository $participantRepository,
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
            $summonerData = $this->summonerDataProvider->getDataByName($data['gameName'], $data['gameTag']);

            $participantsData[] = [
                  'summonerId' => $summonerData['id'],
                  'summonerName' => $summonerData['name'],
                  'puuid' => $summonerData['puuid'],
                  'teamId' => null,
                  'championId' => null,
              ];
        }

        return new Response($serializer->serialize(['info' => $this->gameProvider->connectParticipants(['participants' => $participantsData], null)], 'json'));
    }

    #[Route('/summoners/find-by-name/{playerName}', name: 'find-by-name', methods: ['GET'])]
    public function findByName(Request $request, string $playerName): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $content = json_decode($request->getContent(), true);

        $participantsData = [];

        if (strlen($playerName) < 3) {
            return new Response($serializer->serialize(['info' => $this->gameProvider->connectParticipants(['participants' => $participantsData], null)], 'json'));
        }

        $participants = $this->participantRepository
            ->createQueryBuilder('p')
            ->select('p.puuid,  p.summonerId')
            ->where('p.summonerName LIKE :name')
            ->setParameter('name', '%' . $playerName . '%')
            ->groupBy('p.puuid', 'p.summonerId')
            ->getQuery()
            ->getResult()
        ;

        $participantsExact = $this->participantRepository
            ->createQueryBuilder('p')
            ->select('p.puuid,  p.summonerId')
            ->where('p.summonerName = :name')
            ->setParameter('name',  $playerName)
            ->groupBy('p.puuid', 'p.summonerId')
            ->getQuery()
            ->getResult()
        ;

        $participants = array_merge($participants, $participantsExact);

//        return new Response($serializer->serialize(['info' => $participants], 'json'));

        foreach ($participants as $participant) {
            $participant['teamId'] = null;
            $participant['championId'] = null;

            $participantsData[] = $participant;
        }

        return new Response($serializer->serialize(['info' => $this->gameProvider->connectParticipants(['participants' => $participantsData], null)], 'json'));
    }


    #[Route('/summoner/{summonerName}/count', name: 'summoner-count', methods: ['GET'])]
    public function count(string $summonerName): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $summonerUuid = $this->summonerDataProvider->getPuuidByName($summonerName);

        return new Response($serializer->serialize(['games' => $this->gameRepository->countAllGamesWithPlayer($summonerUuid)], 'json'));
    }
}
