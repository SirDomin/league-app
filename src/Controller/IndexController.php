<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/index', name: 'index')]
    public function index(): JsonResponse
    {
        $output=null;
        $retval=null;
        exec('wmic process | find "LeagueClientUx.exe"', $output, $retval);

        dd($output);
    }
}
//curl -k -X GET -H "Content-Type: application/json" -H "Authorization: Basic trlq-ijV4GtEFvTpn83qqA" "https://127.0.0.1:63451/chat/v5/participants/champ-select"
//curl -X GET -H "Content-Type: application/json" -H "Authorization: Basic FcuztF5isP7JJxDZ29P7oA" "https://127.0.0.1:63533/chat/v5/participants/champ-select"
//1427734738000
