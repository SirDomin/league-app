<?php

namespace App\Repository;

use App\Entity\Challenge;
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

    public function getChallengeAveragesForPosition(string $position, ?int $queueId, array $metrics): array
    {
        $fields = [];
        foreach ($metrics as $metric) {
            $field = lcfirst($metric);
            if (property_exists(Challenge::class, $field)) {
                $fields[] = $field;
            }
        }

        if ($fields === []) {
            return [];
        }

        $select = array_map(
            fn(string $field): string => sprintf('AVG(challenge.%1$s) AS %1$s', $field),
            $fields
        );

        $qb = $this->createQueryBuilder('participant')
            ->select($select)
            ->leftJoin('participant.challenge', 'challenge')
            ->leftJoin('participant.info', 'info')
            ->where('participant.individualPosition = :position')
            ->andWhere('challenge.id IS NOT NULL')
            ->setParameter('position', $position)
        ;

        if ($queueId !== null) {
            $qb
                ->andWhere('info.queueId = :queueId')
                ->setParameter('queueId', $queueId)
            ;
        }

        $averages = $qb->getQuery()->getOneOrNullResult() ?? [];

        $averages = array_map(
            fn($average): ?float => $average !== null ? (float) $average : null,
            $averages
        );

        if ($queueId !== null && array_filter($averages, fn($average): bool => $average !== null) === []) {
            return $this->getChallengeAveragesForPosition($position, null, $metrics);
        }

        return $averages;
    }
}
