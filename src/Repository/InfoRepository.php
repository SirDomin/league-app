<?php

namespace App\Repository;

use App\Entity\Info;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InfoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Info::class);
    }

    public function getDataForField(string $fieldName)
    {
        $qb = $this->createQueryBuilder('i')
            ->select('i.' . $fieldName)
            ->addGroupBy('i.' . $fieldName);

        $result = $qb->getQuery()->getResult();

        return array_map(function($item) use ($fieldName) {
            return $item[$fieldName];
        }, $result);
    }
}
