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
            if (isset($filters['allyTeam']['players']) && is_array($filters['allyTeam']['players'])) {
                $this->applyTeamPlayerFilters($qb, $filters['allyTeam']['players'], 'allyTeamPlayer', 'participantWithPuuid.teamId');
            } else {
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
        }

        if (isset($filters['enemyTeam']) && $filters['enemyTeam'] !== []) {
            if (isset($filters['enemyTeam']['players']) && is_array($filters['enemyTeam']['players'])) {
                $this->applyTeamPlayerFilters($qb, $filters['enemyTeam']['players'], 'enemyTeamPlayer', null, true);
            } else {
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
            if (isset($filters['info']['queueGroup']) && $filters['info']['queueGroup'] === 'ranked') {
                $qb->andWhere('i.queueId IN (:rankedQueueIds)')
                    ->setParameter('rankedQueueIds', [420, 440]);
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

    private function applyTeamPlayerFilters($qb, array $players, string $aliasPrefix, ?string $teamExpression = null, bool $enemyTeam = false): void
    {
        $enemyTeamAlias = null;

        foreach (array_values($players) as $index => $player) {
            if (!is_array($player) || (empty($player['puuid']) && empty($player['gameName']))) {
                continue;
            }

            $alias = $aliasPrefix . $index;
            $joinCondition = $teamExpression
                ? $alias . '.teamId = ' . $teamExpression
                : $alias . '.teamId != participantWithPuuid.teamId';

            if ($enemyTeam && $enemyTeamAlias !== null) {
                $joinCondition = $alias . '.teamId = ' . $enemyTeamAlias . '.teamId';
            }

            $qb->innerJoin('i.participants', $alias, 'WITH', $joinCondition);
            $identityConditions = $qb->expr()->orX();
            $hasIdentityCondition = false;

            if (!empty($player['puuid'])) {
                $identityConditions->add($alias . '.puuid = :' . $alias . 'Puuid');
                $qb->setParameter($alias . 'Puuid', $player['puuid']);
                $hasIdentityCondition = true;
            }

            if (!empty($player['gameName'])) {
                $identityConditions->add($alias . '.riotIdGameName LIKE :' . $alias . 'GameName');
                $identityConditions->add($alias . '.summonerName LIKE :' . $alias . 'GameName');
                $qb->setParameter($alias . 'GameName', '%' . $player['gameName'] . '%');
                $hasIdentityCondition = true;
            }

            if ($hasIdentityCondition) {
                $qb->andWhere($identityConditions);
            }

            if (!empty($player['individualPosition'])) {
                $qb->andWhere($alias . '.individualPosition = :' . $alias . 'Position')
                    ->setParameter($alias . 'Position', $player['individualPosition']);
            }

            if (!empty($player['championId'])) {
                $qb->andWhere($alias . '.championId = :' . $alias . 'ChampionId')
                    ->setParameter($alias . 'ChampionId', (int) $player['championId']);
            }

            if ($enemyTeam && $enemyTeamAlias === null) {
                $enemyTeamAlias = $alias;
            }
        }
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

    public function getAllGamesWithPlayer(string $puuid, int $limit = 0, int $start = 0): array
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

        if ($start > 0) {
            $query->setFirstResult($start);
        }

        if ($limit > 0) {
            $query->setMaxResults($limit);
        }

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
        return (int) $this
            ->createQueryBuilder('g')
            ->select('COUNT(DISTINCT g.id)')
            ->leftJoin('g.info', 'i')
            ->leftJoin('i.participants', 'participant')
            ->where('participant.puuid = :puuid')
            ->setParameter('puuid', $puuid)
            ->getQuery()
            ->getSingleScalarResult();
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
