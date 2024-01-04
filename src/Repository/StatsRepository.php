<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class StatsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function getWinratioByChampion($summonerId, $championId, $myTeam = true): array
    {
        $sumOfWinsQuery = $this->createQueryBuilder('g')
            ->select('COUNT(DISTINCT g.id) as count')
            ->leftJoin('g.info', 'gi')
            ->leftJoin('gi.participants', 'p')
            ->andWhere('p.championId = :championId')
            ->andWhere('gi.queueId = 420')
            ->setParameter('summonerId', $summonerId)
            ->setParameter('championId', $championId)
        ;

        if ($myTeam === true) {
            $sumOfWinsQuery = $sumOfWinsQuery
                ->andWhere('p.teamId = (
                SELECT pp.teamId
                FROM ' . Participant::class . ' pp
                WHERE pp.info = gi
                AND pp.summonerId = :summonerId
                AND pp.win = true
            )');
        } else {
            $sumOfWinsQuery = $sumOfWinsQuery
                ->andWhere('p.teamId != (
                SELECT pp.teamId
                FROM ' . Participant::class . ' pp
                WHERE pp.info = gi
                AND pp.summonerId = :summonerId
                AND pp.win = false
            )');
        }

        $sumOfWinsQuery = $sumOfWinsQuery->getQuery()->getResult();

        $sumOfLosesQuery = $this->createQueryBuilder('g')
            ->select('COUNT(DISTINCT g.id) as count')
            ->leftJoin('g.info', 'gi')
            ->leftJoin('gi.participants', 'p')
            ->andWhere('p.championId = :championId')
            ->andWhere('gi.queueId = 420')
            ->setParameter('summonerId', $summonerId)
            ->setParameter('championId', $championId)
        ;

        if ($myTeam === true) {
            $sumOfLosesQuery = $sumOfLosesQuery
                ->andWhere('p.teamId = (
                SELECT pp.teamId
                FROM ' . Participant::class . ' pp
                WHERE pp.info = gi
                AND pp.summonerId = :summonerId
                AND pp.win = false
            )');
        } else {
            $sumOfLosesQuery = $sumOfLosesQuery
                ->andWhere('p.teamId != (
                SELECT pp.teamId
                FROM ' . Participant::class . ' pp
                WHERE pp.info = gi
                AND pp.summonerId = :summonerId
                AND pp.win = true
            )');
        }
        $sumOfLosesQuery = $sumOfLosesQuery->getQuery()->getResult();

        $sumOfWins = $sumOfWinsQuery[0]['count'] ?? 0;
        $sumOfLoses = $sumOfLosesQuery[0]['count'] ?? 0;

        $totalGames = $sumOfWins + $sumOfLoses;

        $winRate = ($totalGames != 0) ? round(($sumOfWins / $totalGames) * 100, 2) : 0;

        return [
            'wins' => $sumOfWinsQuery[0]['count'],
            'loses' => $sumOfLosesQuery[0]['count'],
            'wr' => $winRate
        ];
    }

    public function getWinratioForSinglePlayerByChampion($summonerId, $championId): array
    {
        $sumOfWinsQuery = $this->createQueryBuilder('g')
            ->select('COUNT(DISTINCT g.id) as count')
            ->leftJoin('g.info', 'gi')
            ->leftJoin('gi.participants', 'p')
            ->andWhere('p.championId = :championId')
            ->andWhere('p.summonerId = :summonerId')
            ->andWhere('p.win = true')
            ->andWhere('gi.queueId = 420')
            ->setParameter('summonerId', $summonerId)
            ->setParameter('championId', $championId)
        ;

        $sumOfWinsQuery = $sumOfWinsQuery->getQuery()->getResult();

        $sumOfLosesQuery = $this->createQueryBuilder('g')
            ->select('COUNT(DISTINCT g.id) as count')
            ->leftJoin('g.info', 'gi')
            ->leftJoin('gi.participants', 'p')
            ->andWhere('p.championId = :championId')
            ->andWhere('p.summonerId = :summonerId')
            ->andWhere('p.win = false')
            ->andWhere('gi.queueId = 420')
            ->setParameter('summonerId', $summonerId)
            ->setParameter('championId', $championId)
        ;

        $sumOfLosesQuery = $sumOfLosesQuery->getQuery()->getResult();

        $sumOfWins = $sumOfWinsQuery[0]['count'] ?? 0;
        $sumOfLoses = $sumOfLosesQuery[0]['count'] ?? 0;

        $totalGames = $sumOfWins + $sumOfLoses;

        $winRate = ($totalGames != 0) ? round(($sumOfWins / $totalGames) * 100, 2) : 0;

        return [
            'wins' => $sumOfWinsQuery[0]['count'],
            'loses' => $sumOfLosesQuery[0]['count'],
            'wr' => $winRate
        ];
    }

    public function getAvailableQueues(string $summonerId): array
    {
        $queues = $this->createQueryBuilder('g')
            ->select('DISTINCT gi.queueId')
            ->leftJoin('g.info', 'gi')
            ->leftJoin('gi.participants', 'p')
            ->where('p.summonerId = :summonerId')
            ->setParameter('summonerId', $summonerId)
        ;

        return $queues->getQuery()->getResult();
    }

    public function getAvailableSeasons(string $summonerId): array
    {
        $seasons = $this->createQueryBuilder('g')
            ->select('gi.gameVersion')
            ->leftJoin('g.info', 'gi')
            ->leftJoin('gi.participants', 'p')
            ->where('p.summonerId = :summonerId')
            ->setParameter('summonerId', $summonerId)
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

    public function getWinratioForAllChampions($summonerId, ?int $queueId = null, ?int $season = null): array
    {
        $sumOfWinsAndLosesQuery = $this->createQueryBuilder('g')
            ->select('p.championId as championId,
                SUM(CASE WHEN p.win = true THEN 1 ELSE 0 END) as wins,
                SUM(CASE WHEN p.win = false THEN 1 ELSE 0 END) as loses,
                COUNT(DISTINCT g.id) as total
            ')
            ->addSelect('p.championName')
            ->leftJoin('g.info', 'gi')
            ->leftJoin('gi.participants', 'p')
            ->andWhere('p.summonerId = :summonerId')
            ->groupBy('p.championId')
            ->addGroupBy('p.championName')
            ->addGroupBy('p.championName')
            ->setParameter('summonerId', $summonerId)
        ;

        if ($queueId !== null) {
            $sumOfWinsAndLosesQuery =
                $sumOfWinsAndLosesQuery
                    ->andWhere('gi.queueId = :queueId')
                    ->setParameter('queueId', $queueId)
                ;
        } else {
            $sumOfWinsAndLosesQuery =
                $sumOfWinsAndLosesQuery
                    ->andWhere('gi.queueId IN (400, 420, 430, 440)')
            ;
        }

        if ($season !== null) {
            $sumOfWinsAndLosesQuery =
                $sumOfWinsAndLosesQuery
                    ->andWhere('gi.gameVersion LIKE :season')
                    ->setParameter('season', $season . '%')
            ;
        }

        $results = $sumOfWinsAndLosesQuery->getQuery()->getResult();

        foreach ($results as &$result) {
            $result['winRatio'] = ($result['total'] > 0) ? round(($result['wins'] * 100) / $result['total'], 2) : 100.0;
        }

        return $results;
    }

    public function getInfoAboutChampion($summonerId, int $queueId, int $championId, string $position, int $season = 0): array
    {
        $query =
            $this->createQueryBuilder('g')
                ->select('participant.championId as championId,
                SUM(CASE WHEN participant.win = false THEN 1 ELSE 0 END) as wins,
                SUM(CASE WHEN participant.win = true THEN 1 ELSE 0 END) as loses,
                COUNT(DISTINCT g.id) as total
            ')
                ->addSelect('participant.championName')
                ->leftJoin('g.info', 'i')
                ->leftJoin('i.participants', 'participant')
                ->where('i.queueId = :queueId')
                ->andWhere('participant.summonerId != :summonerId')
                ->andWhere('participant.teamId != (
                    SELECT p.teamId
                    FROM ' . Participant::class . ' p
                    WHERE p.info = i
                    AND p.summonerId = :summonerId
                    AND p.championId = :championId
                )')
                ->groupBy('participant.championId')
                ->addGroupBy('participant.championName')
                ->setParameter('queueId', $queueId)
                ->setParameter('summonerId', $summonerId)
                ->setParameter('championId', $championId)
            ;

        if ($position === 'same') {
            $query =
                $query->andWhere('participant.individualPosition = (
                    SELECT pp.individualPosition
                    FROM ' . Participant::class . ' pp
                    WHERE pp.info = i
                    AND pp.summonerId = :summonerId
                    AND pp.championId = :championId
                )');
        }

        if ($season !== 0) {
            $query =
                $query
                    ->andWhere('i.gameVersion LIKE :season')
                    ->setParameter('season', $season . '%')
            ;
        }
        $results = $query->getQuery()->getResult();

        if (sizeof($filteredResults = array_filter($results, function ($item) {return $item['total'] > 10;})) > 5) {
            $results = $filteredResults;
        } else if (sizeof($filteredResults = array_filter($results, function ($item) {return $item['total'] > 5;})) > 5) {
            $results = $filteredResults;
        }else if (sizeof($filteredResults = array_filter($results, function ($item) {return $item['total'] > 2;})) > 5) {
            $results = $filteredResults;
        }

        foreach ($results as &$result) {
            $result['winRatio'] = ($result['total'] > 0) ? round(($result['wins'] * 100) / $result['total'], 2) : 100.0;
        }

        usort($results, function($a, $b) {
            return $a['winRatio'] <=> $b['winRatio'];
        });

        $sortedByWinRatioAsc = array_slice($results, 0, 5);

        usort($results, function($a, $b) {
            return $b['winRatio'] <=> $a['winRatio'];
        });

        $sortedByWinRatioDesc = array_slice($results, 0, 5);

        usort($results, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        $sortedByTotal = array_slice($results, 0, 5);

        return [
            'total' => $sortedByTotal,
            'winratio_asc' => $sortedByWinRatioAsc,
            'winratio_desc' => $sortedByWinRatioDesc,
        ];
    }
}
