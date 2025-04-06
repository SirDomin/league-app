<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function getDistinctDataForField(string $fieldName): array
    {
        return [];
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
            ->addSelect('g.backfilled')
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

    public function getAvailableSeasons(): array
    {
        $seasons = $this->createQueryBuilder('g')
            ->select('gi.gameVersion')
            ->leftJoin('g.info', 'gi')
            ->leftJoin('gi.participants', 'p')
            ->groupBy('gi.gameVersion')
            ->getQuery()
            ->getResult()
        ;

        $seasonsArray = [];

        foreach ($seasons as $season) {
            $seasonsArray[] = explode('.', $season['gameVersion'])[0];
        }

        $uniqueSeasons = array_values(array_map('intval', array_unique($seasonsArray)));
        sort($uniqueSeasons);

        return $uniqueSeasons;
    }

    public function paginateFilteredHistory(string $puuid, int $start, int $limit, array $filters): array
    {
        $qb = $this
            ->createQueryBuilder('g')
            ->addSelect('g.id')
            ->addSelect('g.backfilled')
            ->addSelect('i')
            ->addSelect("i.gameCreation AS gameCreation")
            ->addSelect('m')
            ->leftJoin('g.info', 'i')
            ->leftJoin('g.metadata', 'm')
            ->leftJoin('i.participants', 'participants')
            ->leftJoin('i.participants', 'participantWithPuuid', 'WITH', 'participantWithPuuid.puuid = :puuid')
            ->andWhere('participantWithPuuid.puuid IS NOT NULL')
            ->setParameter('puuid', $puuid)
            ->addOrderBy('i.gameStartTimestamp', 'DESC')
        ;

        if (isset($filters['allyTeam']) && $filters['allyTeam'] !== []) {
            $qb->innerJoin('i.participants', 'sameTeamParticipants', 'WITH', 'sameTeamParticipants.teamId = participantWithPuuid.teamId');

            foreach ($filters['allyTeam'] as $allyTeamFilterName => $filterData) {
                if ($allyTeamFilterName === 'riotIdGameName') {
                    $qb->andWhere('sameTeamParticipants.' . $allyTeamFilterName . ' LIKE :' . $allyTeamFilterName)
                        ->setParameter($allyTeamFilterName, '%' . $filterData . '%');
                    $qb->orWhere('sameTeamParticipants.summonerName LIKE :' . $allyTeamFilterName)
                        ->setParameter($allyTeamFilterName, '%' . $filterData . '%');
                } else {
                    $qb->andWhere('sameTeamParticipants.' . $allyTeamFilterName . ' = :allyTeam' . $allyTeamFilterName)
                        ->setParameter('allyTeam' . $allyTeamFilterName, $filterData);

                }
            }
        }

        if (isset($filters['enemyTeam']) && $filters['enemyTeam'] !== []) {
            $qb->innerJoin('i.participants', 'enemyTeamParticipants', 'WITH', 'enemyTeamParticipants.teamId != participantWithPuuid.teamId');

            foreach ($filters['enemyTeam'] as $enemyTeamFilterName => $filterData) {
                if ($enemyTeamFilterName === 'riotIdGameName') {
                    $qb->andWhere('enemyTeamParticipants.' . $enemyTeamFilterName . ' LIKE :' . $enemyTeamFilterName)
                        ->setParameter($enemyTeamFilterName, '%' . $filterData . '%');
                    $qb->orWhere('enemyTeamParticipants.summonerName LIKE :' . $enemyTeamFilterName)
                        ->setParameter($enemyTeamFilterName, '%' . $filterData . '%');
                } else {
                    $qb->andWhere('enemyTeamParticipants.' . $enemyTeamFilterName . ' = :enemyTeam' . $enemyTeamFilterName)
                        ->setParameter('enemyTeam' . $enemyTeamFilterName, $filterData);
                }

            }
        }

        foreach($filters['activePlayer'] as $activePlayerFilterName => $filterData) {
            $qb->andWhere('participantWithPuuid.' . $activePlayerFilterName . ' = :' . 'activePlayer'. $activePlayerFilterName)
                ->setParameter('activePlayer'.$activePlayerFilterName, $filterData);
        }

        foreach ($filters['metadata'] as $metadataFilterName => $filterData) {
            $qb->andWhere('m.' . $metadataFilterName . ' LIKE :' . $metadataFilterName)
                ->setParameter($metadataFilterName, '%' . $filterData . '%');
        }

        if (isset($filters['info'])) {
            if (isset($filters['info']['season'])) {
                $qb->andWhere('i.gameVersion LIKE :season')
                    ->setParameter('season', $filters['info']['season'] . '%');
            }
            if (isset($filters['info']['queueId'])) {
                $qb->andWhere('i.queueId = :queueId')
                    ->setParameter('queueId', (int) $filters['info']['queueId']);
            }
        }

        $ids = [];
        $res = $qb->getQuery()->getResult();

        /** @var Game $game */
        foreach ($res as $game) {
            if (isset($filters['info']) && isset($filters['info']['dayOfWeek'])) {
                $date = new \DateTime('@' . $game['gameCreation'] / 1000);

                if ((int) $date->format('N') !== (int) $filters['info']['dayOfWeek']) {
                    continue;
                }
            }

            $ids[] = $game['id'];
        }

        $minimizedGames = $this->getAllGamesMinimized($ids, $start, $limit);


        return $minimizedGames;

    }

    private function getAllGamesMinimized(array $gameIds, int $start, int $limit): array {
        $result = $this
            ->createQueryBuilder('g')
            ->addSelect('g')
            ->addSelect('m')
            ->addSelect('i')
            ->addSelect('participant')
            ->leftJoin('g.metadata', 'm')
            ->leftJoin('g.info', 'i')
            ->leftJoin('i.participants', 'participant')
            ->where('g.id IN (:ids)')
            ->addOrderBy('i.gameStartTimestamp', 'DESC')
            ->setParameter(':ids', $gameIds)
        ;

        return $result->getQuery()->getResult();
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

    public function getGamesToBackfill(): array {
        $result = $this
            ->createQueryBuilder('g')
            ->addSelect('g.id')
            ->addSelect('g.backfilled')
            ->addSelect('i')
            ->leftJoin('g.info', 'i')
            ->where('g.backfilled = false')
            ->addOrderBy('i.gameStartTimestamp', 'DESC')
            ->setMaxResults(50)
        ;

        $ids = [];
        $res = $result->getQuery()->getResult();

        foreach ($res as $game) {
            $ids[] = $game['id'];
        }

        return $this->getGames($ids);
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
            ->addOrderBy('i.gameStartTimestamp', 'DESC')
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
            ->addOrderBy('i.gameCreation', 'DESC')
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
