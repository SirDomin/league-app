<?php

namespace App\Repository;

use App\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
            ->addSelect('participant')
            ->addSelect('challenge')
            ->leftJoin('g.metadata', 'm')
            ->leftJoin('g.info', 'i')
            ->leftJoin('i.participants', 'participant')
            ->leftJoin('participant.challenge', 'challenge')
            ->where('m.matchId = :matchId')
            ->setParameter('matchId', $matchId)
        ;

        try {
            return $result->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException) {
            dd($matchId);
        }
    }

    public function paginateHistory(int $timestamp, int $limit): array
    {
        $result = $this
            ->createQueryBuilder('g')
            ->addSelect('g.id')
            ->addSelect('i')
            ->leftJoin('g.info', 'i')
            ->where('i.gameStartTimestamp < :timestamp')
            ->setParameter('timestamp', $timestamp)
            ->addOrderBy('i.gameStartTimestamp', 'DESC')
            ->setMaxResults($limit)
        ;

        $ids = [];
        $res = $result->getQuery()->getResult();

        foreach ($res as $game) {
            $ids[] = $game['id'];
        }

        return $this->getGames($ids);
    }

    private function getGames(array $gameIds): array {
        $result = $this
            ->createQueryBuilder('g')
            ->addSelect('g')
            ->addSelect('m')
            ->addSelect('i')
            ->addSelect('participant')
            ->addSelect('challenge')
            ->leftJoin('g.metadata', 'm')
            ->leftJoin('g.info', 'i')
            ->leftJoin('i.participants', 'participant')
            ->leftJoin('participant.challenge', 'challenge')
            ->where('g.id IN (:ids)')
            ->addOrderBy('i.gameStartTimestamp', 'DESC')
            ->setParameter(':ids', $gameIds)
        ;

        return $result->getQuery()->getResult();
    }

    public function getAllGamesWithPlayer(string $puuid): array
    {
        $query = $this
            ->createQueryBuilder('g')
            ->addSelect('g')
            ->addSelect('i')
            ->addSelect('participant')
            ->addSelect('metadata')
            ->leftJoin('g.info', 'i')
            ->leftJoin('g.metadata', 'metadata')
            ->leftJoin('i.participants', 'participant')
            ->leftJoin('participant.challenge', 'challenge')
            ->where('participant.puuid = :puuid')
            ->setParameter('puuid', $puuid)
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function getAllGamesWithPlayerBySummonerId(string $summonerId): array
    {
        $query = $this
            ->createQueryBuilder('g')
            ->addSelect('g')
            ->addSelect('i')
            ->addSelect('participant')
            ->addSelect('metadata')
            ->leftJoin('g.info', 'i')
            ->leftJoin('g.metadata', 'metadata')
            ->leftJoin('i.participants', 'participant')
            ->leftJoin('participant.challenge', 'challenge')
            ->where('participant.summonerId = :summonerId')
            ->setParameter('summonerId', $summonerId)
            ->orderBy('i.gameCreation', 'DESC')
            ->setMaxResults(1000)
        ;

        return $query->getQuery()->getArrayResult();
    }

    public function countAllGamesWithPlayerBySummonerId(string $summonerId): int
    {
        $result = $this
            ->createQueryBuilder('g')
            ->addSelect('g')
            ->leftJoin('g.info', 'i')
            ->leftJoin('i.participants', 'participant')
            ->leftJoin('participant.challenge', 'challenge')
            ->where('participant.summonerId = :summonerId')
            ->setParameter('summonerId', $summonerId)
        ;

        return count($result->getQuery()->getArrayResult());
    }

    public function countAllGamesWithPlayer(string $puuid): int
    {
        $results = $this->getGamesWithPlayer($puuid);

        return count($results);
    }

    public function getGameByInfoId(int $id): ?Game
    {
        $queryBuilder = $this
            ->createQueryBuilder('g')
            ->select('g')
            ->addSelect('i')
            ->leftJoin('g.info', 'i')
            ->where('i.id = :id')
            ->setParameter('id', $id)
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function getLastGame(): Game
    {
        $queryBuilder = $this
            ->createQueryBuilder('g')
            ->select('g')
            ->addSelect('i')
            ->leftJoin('g.info', 'i')
            ->setMaxResults(1)
            ->orderBy('i.gameCreation', 'DESC')
        ;

        $game = $queryBuilder->getQuery()->getOneOrNullResult();

        $query = $this
            ->createQueryBuilder('g')
            ->addSelect('g')
            ->addSelect('i')
            ->addSelect('participant')
            ->addSelect('metadata')
            ->leftJoin('g.info', 'i')
            ->leftJoin('g.metadata', 'metadata')
            ->leftJoin('i.participants', 'participant')
            ->leftJoin('participant.challenge', 'challenge')
            ->where('g.id = :id')
            ->setParameter('id', $game->getId())
            ->orderBy('participant.teamId', 'ASC')
        ;

        return $query->getQuery()->getOneOrNullResult();
    }

    private function getGamesWithPlayer(string $puuid): array
    {
        $result = $this
            ->createQueryBuilder('g')
            ->addSelect('g')
            ->leftJoin('g.info', 'i')
            ->leftJoin('i.participants', 'participant')
            ->leftJoin('participant.challenge', 'challenge')
            ->where('participant.puuid = :puuid')
            ->setParameter('puuid', $puuid)
        ;

        return $result->getQuery()->getArrayResult();
    }
}
