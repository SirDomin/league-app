<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Participant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

class GameRepository extends ServiceEntityRepository
{
    private const PLAYER_FILTER_OPERATORS = ['=', '>', '<', '>=', '<=', 'contains'];
    private const BLOCKED_PLAYER_FILTER_FIELDS = [
        'id',
        'info',
        'challenge',
        'perks',
        'missions',
        'playerBehavior',
        'score',
        'individualBest',
        'comments',
        'teamRelation',
        'activePlayerWin',
    ];

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

        if (isset($filters['activePlayer']) && is_array($filters['activePlayer'])) {
            foreach($filters['activePlayer'] as $activePlayerFilterName => $filterData) {
                $qb->andWhere('participantWithPuuid.' . $activePlayerFilterName . ' = :' . 'activePlayer'. $activePlayerFilterName)
                    ->setParameter('activePlayer'.$activePlayerFilterName, $filterData);
            }
        }

        if (isset($filters['easyFilters']) && is_array($filters['easyFilters'])) {
            $this->applyActivePlayerEasyFilters($qb, $filters['easyFilters'], $puuid);
        }

        if (isset($filters['playerRules']) && is_array($filters['playerRules'])) {
            $this->applyPlayerRuleFilters($qb, $filters['playerRules']);
        }

        if (isset($filters['metadata']) && is_array($filters['metadata'])) {
            foreach ($filters['metadata'] as $metadataFilterName => $filterData) {
                $qb->andWhere('m.' . $metadataFilterName . ' LIKE :' . $metadataFilterName)
                    ->setParameter($metadataFilterName, '%' . $filterData . '%');
            }
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

        if (isset($filters['advanced']['placement'])) {
            $qb->andWhere('participantWithPuuid.placement = :advancedPlacement')
                ->setParameter('advancedPlacement', (int) $filters['advanced']['placement']);
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

    private function applyActivePlayerEasyFilters($qb, array $filters, string $puuid): void
    {
        if (!empty($filters['individualPosition'])) {
            $qb->andWhere('participantWithPuuid.individualPosition = :easyIndividualPosition')
                ->setParameter('easyIndividualPosition', $filters['individualPosition']);
        }

        if (!empty($filters['championId'])) {
            $qb->andWhere('participantWithPuuid.championId = :easyChampionId')
                ->setParameter('easyChampionId', (int) $filters['championId']);
        }

        if (!empty($filters['itemId'])) {
            $itemExpression = $qb->expr()->orX();
            for ($slot = 0; $slot <= 6; $slot++) {
                $itemExpression->add('participantWithPuuid.item' . $slot . ' = :easyItemId');
            }
            $qb->andWhere($itemExpression)
                ->setParameter('easyItemId', (int) $filters['itemId']);
        }

        if (!empty($filters['runeId'])) {
            $gameIds = $this->findGameIdsByActivePlayerRune($puuid, (int) $filters['runeId']);
            if ($gameIds === []) {
                $qb->andWhere('g.id = -1');
                return;
            }

            $qb->andWhere('g.id IN (:easyRuneGameIds)')
                ->setParameter('easyRuneGameIds', $gameIds);
        }
    }

    private function findGameIdsByActivePlayerRune(string $puuid, int $runeId): array
    {
        if ($runeId <= 0) {
            return [];
        }

        return $this->getEntityManager()->getConnection()->fetchFirstColumn(
            'SELECT g.id
             FROM game g
             INNER JOIN info i ON g.info_id = i.id
             INNER JOIN participant p ON p.info_id = i.id
             WHERE p.puuid = :puuid
               AND (p.perks::text LIKE :compactRune OR p.perks::text LIKE :spacedRune)',
            [
                'puuid' => $puuid,
                'compactRune' => '%"perk":' . $runeId . '%',
                'spacedRune' => '%"perk": ' . $runeId . '%',
            ]
        );
    }

    private function applyPlayerRuleFilters($qb, array $rules): void
    {
        foreach (array_values($rules) as $index => $rule) {
            if (!is_array($rule)) {
                continue;
            }

            $alias = 'playerRule' . $index;
            $player = $rule['player'] ?? [];
            $isCurrentPlayer = isset($player['type']) && $player['type'] === 'me';
            $isAnyPlayer = isset($player['type']) && $player['type'] === 'any';

            if ($isCurrentPlayer) {
                $alias = 'participantWithPuuid';
            } elseif ($isAnyPlayer) {
                $qb->innerJoin('i.participants', $alias);
            } else {
                $qb->innerJoin('i.participants', $alias);
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

                if (!$hasIdentityCondition) {
                    continue;
                }

                $qb->andWhere($identityConditions);
                $teamRelation = $rule['teamRelation'] ?? null;
                if ($teamRelation === 'ally') {
                    $qb->andWhere($alias . '.teamId = participantWithPuuid.teamId');
                }
                if ($teamRelation === 'enemy') {
                    $qb->andWhere($alias . '.teamId != participantWithPuuid.teamId');
                }
            }

            if (!empty($rule['individualPosition'])) {
                $qb->andWhere($alias . '.individualPosition = :' . $alias . 'Position')
                    ->setParameter($alias . 'Position', $rule['individualPosition']);
            }

            if (!empty($rule['championId'])) {
                $qb->andWhere($alias . '.championId = :' . $alias . 'ChampionId')
                    ->setParameter($alias . 'ChampionId', (int) $rule['championId']);
            }

            $conditions = $this->normalizePlayerRuleConditions($rule);
            if ($conditions !== []) {
                $this->applyPlayerFieldConditions($qb, $alias, $conditions, $rule['conditionMode'] ?? 'and', $index);
            }
        }
    }

    private function normalizePlayerRuleConditions(array $rule): array
    {
        $conditions = [];

        if (isset($rule['conditions']) && is_array($rule['conditions'])) {
            $conditions = $rule['conditions'];
        } elseif (!empty($rule['field']) && isset($rule['value'])) {
            $conditions = [[
                'field' => $rule['field'],
                'operator' => $rule['operator'] ?? '=',
                'value' => $rule['value'],
            ]];
        }

        return array_values(array_filter($conditions, static function ($condition) {
            return is_array($condition)
                && !empty($condition['field'])
                && isset($condition['value'])
                && $condition['value'] !== '';
        }));
    }

    private function applyPlayerFieldConditions($qb, string $alias, array $conditions, string $conditionMode, int $ruleIndex): void
    {
        $conditionExpression = strtolower($conditionMode) === 'or'
            ? $qb->expr()->orX()
            : $qb->expr()->andX();
        $hasConditionExpression = false;

        foreach ($conditions as $conditionIndex => $condition) {
            $expression = $this->buildPlayerFieldCondition($qb, $alias, $condition, $ruleIndex, $conditionIndex);
            if ($expression !== null) {
                $conditionExpression->add($expression);
                $hasConditionExpression = true;
            }
        }

        if ($hasConditionExpression) {
            $qb->andWhere($conditionExpression);
        }
    }

    private function buildPlayerFieldCondition($qb, string $alias, array $condition, int $ruleIndex, int $conditionIndex): ?string
    {
        $field = $condition['field'];
        $operator = $condition['operator'] ?? '=';

        if (!$this->isAllowedPlayerFilterField($field) || !in_array($operator, self::PLAYER_FILTER_OPERATORS, true)) {
            return null;
        }

        $fieldType = $this->getPlayerFilterFieldType($field);
        if (!$this->isOperatorAllowedForFieldType($operator, $fieldType)) {
            return null;
        }

        $parameterName = 'playerRule' . $ruleIndex . 'Condition' . $conditionIndex . ucfirst($field);
        if ($operator === 'contains') {
            $qb->setParameter($parameterName, '%' . $condition['value'] . '%');
            return $alias . '.' . $field . ' LIKE :' . $parameterName;
        }

        $qb->setParameter($parameterName, $this->normalizePlayerFieldValue($condition['value'], $fieldType));
        return $alias . '.' . $field . ' ' . $operator . ' :' . $parameterName;
    }

    private function isAllowedPlayerFilterField(string $field): bool
    {
        if (!preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $field)) {
            return false;
        }

        if (in_array($field, self::BLOCKED_PLAYER_FILTER_FIELDS, true)) {
            return false;
        }

        return $this->getEntityManager()->getClassMetadata(Participant::class)->hasField($field);
    }

    private function getPlayerFilterFieldType(string $field): ?string
    {
        $metadata = $this->getEntityManager()->getClassMetadata(Participant::class);

        return $metadata->hasField($field) ? $metadata->getTypeOfField($field) : null;
    }

    private function isOperatorAllowedForFieldType(string $operator, ?string $fieldType): bool
    {
        if (in_array($fieldType, ['integer', 'float'], true)) {
            return in_array($operator, ['=', '>', '<', '>=', '<='], true);
        }

        if ($fieldType === 'boolean') {
            return $operator === '=';
        }

        if (in_array($fieldType, ['string', 'text'], true)) {
            return in_array($operator, ['=', 'contains'], true);
        }

        return false;
    }

    private function normalizePlayerFieldValue($value, ?string $fieldType)
    {
        if ($fieldType === 'boolean' && is_string($value)) {
            return strtolower($value) === 'true' || $value === '1';
        }

        if ($fieldType === 'integer') {
            return (int) $value;
        }

        if ($fieldType === 'float') {
            return (float) $value;
        }

        return $value;
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
