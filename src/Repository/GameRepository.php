<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findByMatchId(string $matchId): ?Game
    {
        $result = $this
            ->createQueryBuilder('g')
            ->addSelect('g')
            ->addSelect('m')
            ->addSelect('i')
            ->leftJoin('g.metadata', 'm')
            ->leftJoin('g.info', 'i')
            ->where('m.matchId = :matchId')
            ->setParameter('matchId', $matchId)
        ;

        return $result->getQuery()->getOneOrNullResult();
    }
}
