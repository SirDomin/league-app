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

    #[Route('/test', name: 'test')]
    public function test(): JsonResponse
    {
        $data = ['sad' => 'aaa'];

        $string = 'vxXy6g7aVB982Xmnwbc9nQ==';

        return new JsonResponse([
            'encoded' => $this->leagueApi->encodeKey($data),
            'decoded' => $this->leagueApi->decodeKey($string),
        ]);
    }


    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $request): Response
    {
        $serializer = SerializerBuilder::create()->build();

        $content = json_decode($request->getContent(), true);

        $platformName = strtolower($content['platformData']);

        $summonerData = $this->leagueApi->login($content['summonerData']['displayName'], $platformName);

        if (isset($summonerData['status'])) {
            return new Response($serializer->serialize($summonerData, 'json'), Response::HTTP_UNAUTHORIZED);
        }

        if ($summonerData) {
            if (
                $summonerData['name'] === $content['summonerData']['displayName'] &&
                $summonerData['profileIconId'] === $content['summonerData']['profileIconId'] &&
                $summonerData['summonerLevel'] === $content['summonerData']['summonerLevel']
            ) {
                $dataToSave = [
                    'server' => $platformName,
                    'summonerName' => $summonerData['name'],
                    'puuid' => $summonerData['puuid'],
                ];

                return new Response($serializer->serialize([
                    'token' => $this->leagueApi->encodeKey($dataToSave),
                    'puuid' => $summonerData['puuid'],
                ], 'json'), Response::HTTP_OK);
            }
        }

        return new Response($serializer->serialize([], 'json'), Response::HTTP_UNAUTHORIZED);
    }
}
