<?php

namespace App\Repository;

use App\Entity\Info;
use App\Entity\Metadata;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MetadataRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Metadata::class);
    }

    public function getDataForField(string $fieldName)
    {
        $qb = $this->createQueryBuilder('m')
            ->select('m.' . $fieldName)
            ->addGroupBy('m.' . $fieldName);

        $result = $qb->getQuery()->getResult();

        return array_map(function($item) use ($fieldName) {
            return $item[$fieldName];
        }, $result);
    }
}
