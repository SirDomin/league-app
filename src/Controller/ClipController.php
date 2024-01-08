<?php

namespace App\Controller;

use App\ApiManager\LeagueApi;
use App\Entity\Clip;
use App\Repository\ClipRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClipController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ClipRepository $clipRepository,
    )
    {
    }

    #[Route('/clip/{id}/remove', name: 'remove-clip', methods: ['DELETE'])]
    public function removeClip(int $id): Response
    {
        $clip = $this->clipRepository->find($id);

        if ($clip instanceof Clip) {
            $this->entityManager->remove($clip);
            $this->entityManager->flush();

            return new JsonResponse('', 204);
        }
        return new JsonResponse('', 404);
    }


    #[Route('/clips/all', name: 'clips-all', methods: ['GET'])]
    public function clipsAll(): Response
    {
        $clips = $this->clipRepository->findAll();

        $clipsResult = [];

        /** @var Clip $clip */
        foreach ($clips as $clip) {
            $clipsResult[] = [
                'url' => $clip->getUrl(),
                'info' => $clip->getInfo()->getId(),
                'title' => $clip->getTitle(),
                'matchId' => $clip->getInfo()->getGame()->getMetadata()->getMatchId(),
            ];
        }

        return new JsonResponse([
            'clips' => $clipsResult,
        ]);
    }
}
