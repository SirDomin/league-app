<?php

namespace App\Controller;

use App\ApiManager\LeagueApi;
use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly LeagueApi $leagueApi,
    )
    {
    }

    #[Route('/status', name: 'status')]
    public function index(): JsonResponse
    {
       return new JsonResponse(['status' => 'OK']);
    }


    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $content = json_decode($request->getContent(), true);

        $summonerData = $this->leagueApi->getSummonerData($content['login']);

        return new Response($serializer->serialize(
            [
                'result' => $summonerData['puuid']
            ],
            'json')
        );
    }
}
