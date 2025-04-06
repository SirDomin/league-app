<?php

namespace App\Repository;

use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Participant::class);
    }

    public function getDataForField(string $fieldName): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.' . $fieldName)
            ->addGroupBy('p.' . $fieldName);

        $result = $qb->getQuery()->getResult();

        return array_map(function($item) use ($fieldName) {
            return $item[$fieldName];
        }, $result);
    }

    public function getAllComments(string $puuid): array
    {
        $qb = $this->createQueryBuilder('p')
            ->select('p.comment')
            ->where('p.puuid = :puuid')
            ->setParameter('puuid', $puuid)
        ;

        return $qb->getQuery()->getResult();
    }
}
