<?php

namespace App\Entity;

use App\Transformer\ChallengeTransformer;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="participant")
 */
class Participant
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Info", cascade={"persist"})
     * @ORM\JoinColumn(name="info_id", referencedColumnName="id")
     */
    private $info;

    /**
     * @ORM\Column(options={"default":""})
     */
    private $comment = '';

    /**
     * @ORM\Column(type="integer")
     */
    private $allInPings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $assistMePings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $baitPings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $basicPings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $assists = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $baronKills = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $bountyLevel = 0;

    /**
     * @ORM\OneToOne(targetEntity="Challenge", cascade={"persist"})
     */
    private $challenge;

    /**
     * @ORM\Column(type="integer")
     */
    private $champExperience = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $champLevel = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $championId = 0;

    /**
     * @ORM\Column(type="string")
     */
    private $championName = '';

    /**
     * @ORM\Column(type="integer")
     */
    private $championTransform = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $commandPings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $consumablesPurchased = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $damageDealtToBuildings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $damageDealtToObjectives = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $damageDealtToTurrets = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $damageSelfMitigated = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $dangerPings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $deaths = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $detectorWardsPlaced = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $doubleKills = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $dragonKills = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $eligibleForProgression = false;

    /**
     * @ORM\Column(type="integer")
     */
    private $enemyMissingPings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $enemyVisionPings = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $firstBloodAssist = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $firstBloodKill = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $firstTowerAssist = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $firstTowerKill = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $gameEndedInEarlySurrender  = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $gameEndedInSurrender;

    /**
     * @ORM\Column(type="integer")
     */
    private $getBackPings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $goldEarned = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $goldSpent = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $holdPings = 0;

    /**
     * @ORM\Column(type="string")
     */
    private $individualPosition = '';

    /**
     * @ORM\Column(type="string")
     */
    private $teamPosition = '';

    /**
     * @ORM\Column(type="integer")
     */
    private $inhibitorKills = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $inhibitorTakedowns = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $inhibitorsLost = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $item0;

    /**
     * @ORM\Column(type="integer")
     */
    private $item1;

    /**
     * @ORM\Column(type="integer")
     */
    private $item2;

    /**
     * @ORM\Column(type="integer")
     */
    private $item3;

    /**
     * @ORM\Column(type="integer")
     */
    private $item4;

    /**
     * @ORM\Column(type="integer")
     */
    private $item5;

    /**
     * @ORM\Column(type="integer")
     */
    private $item6;

    /**
     * @ORM\Column(type="integer")
     */
    private $itemsPurchased;

    /**
     * @ORM\Column(type="integer")
     */
    private $killingSprees;

    /**
     * @ORM\Column(type="integer")
     */
    private $kills;

    /**
     * @ORM\Column(type="string")
     */
    private $lane;

    /**
     * @ORM\Column(type="integer")
     */
    private $largestCriticalStrike;

    /**
     * @ORM\Column(type="integer")
     */
    private $largestKillingSpree;

    /**
     * @ORM\Column(type="integer")
     */
    private $largestMultiKill;

    /**
     * @ORM\Column(type="integer")
     */
    private $longestTimeSpentLiving;

    /**
     * @ORM\Column(type="integer")
     */
    private $magicDamageDealt;

    /**
     * @ORM\Column(type="integer")
     */
    private $magicDamageDealtToChampions;

    /**
     * @ORM\Column(type="integer")
     */
    private $magicDamageTaken;

    /**
     * @ORM\Column(type="integer")
     */
    private $needVisionPings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $neutralMinionsKilled;

    /**
     * @ORM\Column(type="integer")
     */
    private $nexusKills;

    /**
     * @ORM\Column(type="integer")
     */
    private $nexusTakedowns;

    /**
     * @ORM\Column(type="integer")
     */
    private $nexusLost;

    /**
     * @ORM\Column(type="integer")
     */
    private $objectivesStolen;

    /**
     * @ORM\Column(type="integer")
     */
    private $objectivesStolenAssists;

    /**
     * @ORM\Column(type="integer")
     */
    private $onMyWayPings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $participantId;

    /**
     * @ORM\Column(type="integer")
     */
    private $pentaKills;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $perks;

    /**
     * @ORM\Column(type="integer")
     */
    private $physicalDamageDealt;

    /**
     * @ORM\Column(type="integer")
     */
    private $physicalDamageDealtToChampions;

    /**
     * @ORM\Column(type="integer")
     */
    private $physicalDamageTaken;

    /**
     * @ORM\Column(type="integer")
     */
    private $profileIcon;

    /**
     * @ORM\Column(type="integer")
     */
    private $pushPings = 0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $puuid;

    /**
     * @ORM\Column(type="integer")
     */
    private $quadraKills;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $riotIdName = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $riotIdTagline;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $role;

    /**
     * @ORM\Column(type="integer")
     */
    private $sightWardsBoughtInGame;

    /**
     * @ORM\Column(type="integer")
     */
    private $spell1Casts;

    /**
     * @ORM\Column(type="integer")
     */
    private $spell2Casts;

    /**
     * @ORM\Column(type="integer")
     */
    private $spell3Casts;

    /**
     * @ORM\Column(type="integer")
     */
    private $spell4Casts;

    /**
     * @ORM\Column(type="integer")
     */
    private $summoner1Casts;

    /**
     * @ORM\Column(type="integer")
     */
    private $summoner1Id;

    /**
     * @ORM\Column(type="integer")
     */
    private $summoner2Casts;

    /**
     * @ORM\Column(type="integer")
     */
    private $summoner2Id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $summonerId;

    /**
     * @ORM\Column(type="integer")
     */
    private $summonerLevel;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $summonerName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $teamEarlySurrendered;

    /**
     * @ORM\Column(type="integer")
     */
    private $teamId;

    /**
     * @ORM\Column(type="integer")
     */
    private $timeCCingOthers;

    /**
     * @ORM\Column(type="integer")
     */
    private $timePlayed;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalDamageDealt;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalDamageDealtToChampions;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalDamageShieldedOnTeammates;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalDamageTaken;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalHeal;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalHealsOnTeammates;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalMinionsKilled;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalTimeCCDealt;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalTimeSpentDead;

    /**
     * @ORM\Column(type="integer")
     */
    private $totalUnitsHealed;

    /**
     * @ORM\Column(type="integer")
     */
    private $tripleKills;

    /**
     * @ORM\Column(type="integer")
     */
    private $trueDamageDealt;

    /**
     * @ORM\Column(type="integer")
     */
    private $trueDamageDealtToChampions;

    /**
     * @ORM\Column(type="integer")
     */
    private $trueDamageTaken;

    /**
     * @ORM\Column(type="integer")
     */
    private $turretKills;

    /**
     * @ORM\Column(type="integer")
     */
    private $turretTakedowns;

    /**
     * @ORM\Column(type="integer")
     */
    private $turretsLost;

    /**
     * @ORM\Column(type="integer")
     */
    private $unrealKills;

    /**
     * @ORM\Column(type="integer")
     */
    private $visionClearedPings = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $visionScore;

    /**
     * @ORM\Column(type="integer")
     */
    private $visionWardsBoughtInGame;

    /**
     * @ORM\Column(type="integer")
     */
    private $wardsKilled;

    /**
     * @ORM\Column(type="integer")
     */
    private $wardsPlaced;

    /**
     * @ORM\Column(type="boolean")
     */
    private $win;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $riotIdGameName = '';

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalAllyJungleMinionsKilled = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalEnemyJungleMinionsKilled = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $placement = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $playerAugment1 = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $playerAugment2 = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $playerAugment3 = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $playerAugment4 = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $playerAugment5 = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $playerAugment6 = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $playerSubteamId = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $subteamPlacement = null;

    private $score = 0;

    private $individualBest = [];

    private $comments = [];

    public function getComments(): array
    {
        return $this->comments;
    }

    public function setComments(array $comments): void
    {
        $this->comments = $comments;
    }

    public function getIndividualBest(): array
    {
        return $this->individualBest;
    }

    public function setIndividualBest(array $individualBest): void
    {
        $this->individualBest = $individualBest;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score)
    {
        $this->score = $score;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getVisionClearedPings()
    {
        return $this->visionClearedPings;
    }

    /**
     * @param mixed $visionClearedPings
     */
    public function setVisionClearedPings($visionClearedPings): void
    {
        $this->visionClearedPings = $visionClearedPings;
    }

    /**
     * @return mixed
     */
    public function getPushPings()
    {
        return $this->pushPings;
    }

    /**
     * @param mixed $pushPings
     */
    public function setPushPings($pushPings): void
    {
        $this->pushPings = $pushPings;
    }

    /**
     * @return mixed
     */
    public function getHoldPings()
    {
        return $this->holdPings;
    }

    /**
     * @param mixed $holdPings
     */
    public function setHoldPings($holdPings): void
    {
        $this->holdPings = $holdPings;
    }

    /**
     * @return mixed
     */
    public function getDangerPings()
    {
        return $this->dangerPings;
    }

    /**
     * @param mixed $dangerPings
     */
    public function setDangerPings($dangerPings): void
    {
        $this->dangerPings = $dangerPings;
    }

    /**
     * @return bool
     */
    public function getEligibleForProgression(): bool
    {
        return $this->eligibleForProgression;
    }

    /**
     * @param bool $eligibleForProgression
     */
    public function setEligibleForProgression(bool $eligibleForProgression): void
    {
        $this->eligibleForProgression = $eligibleForProgression;
    }

    /**
     * @return mixed
     */
    public function getEnemyMissingPings()
    {
        return $this->enemyMissingPings;
    }

    /**
     * @param mixed $enemyMissingPings
     */
    public function setEnemyMissingPings($enemyMissingPings): void
    {
        $this->enemyMissingPings = $enemyMissingPings;
    }

    /**
     * @return mixed
     */
    public function getEnemyVisionPings()
    {
        return $this->enemyVisionPings;
    }

    /**
     * @param mixed $enemyVisionPings
     */
    public function setEnemyVisionPings($enemyVisionPings): void
    {
        $this->enemyVisionPings = $enemyVisionPings;
    }

    /**
     * @return mixed
     */
    public function getGetBackPings()
    {
        return $this->getBackPings;
    }

    /**
     * @param mixed $getBackPings
     */
    public function setGetBackPings($getBackPings): void
    {
        $this->getBackPings = $getBackPings;
    }

    /**
     * @return mixed
     */
    public function getNeedVisionPings()
    {
        return $this->needVisionPings;
    }

    /**
     * @param mixed $needVisionPings
     */
    public function setNeedVisionPings($needVisionPings): void
    {
        $this->needVisionPings = $needVisionPings;
    }

    /**
     * @return mixed
     */
    public function getOnMyWayPings()
    {
        return $this->onMyWayPings;
    }

    /**
     * @param mixed $onMyWayPings
     */
    public function setOnMyWayPings($onMyWayPings): void
    {
        $this->onMyWayPings = $onMyWayPings;
    }

    /**
     * @return mixed
     */
    public function getCommandPings()
    {
        return $this->commandPings;
    }

    /**
     * @param mixed $commandPings
     */
    public function setCommandPings($commandPings): void
    {
        $this->commandPings = $commandPings;
    }

    /**
     * @return mixed
     */
    public function getAllInPings()
    {
        return $this->allInPings;
    }

    /**
     * @param mixed $allInPings
     */
    public function setAllInPings($allInPings): void
    {
        $this->allInPings = $allInPings;
    }

    /**
     * @return mixed
     */
    public function getAssistMePings()
    {
        return $this->assistMePings;
    }

    /**
     * @param mixed $assistMePings
     */
    public function setAssistMePings($assistMePings): void
    {
        $this->assistMePings = $assistMePings;
    }

    /**
     * @return mixed
     */
    public function getBaitPings()
    {
        return $this->baitPings;
    }

    /**
     * @param mixed $baitPings
     */
    public function setBaitPings($baitPings): void
    {
        $this->baitPings = $baitPings;
    }

    /**
     * @return mixed
     */
    public function getBasicPings()
    {
        return $this->basicPings;
    }

    /**
     * @param mixed $basicPings
     */
    public function setBasicPings($basicPings): void
    {
        $this->basicPings = $basicPings;
    }

    /**
     * @return mixed
     */
    public function getChallenges()
    {
        return $this->challenge;
    }

    /**
     * @param mixed $challenge
     */
    public function setChallenges($challenge): void
    {
        $this->challenge = ChallengeTransformer::getChallenge($challenge);

        $this->challenge->setParticipant($this);
    }

    /**
     * @return mixed
     */
    public function getAssists()
    {
        return $this->assists;
    }

    /**
     * @param mixed $assists
     */
    public function setAssists($assists): void
    {
        $this->assists = $assists;
    }

    /**
     * @return mixed
     */
    public function getBaronKills()
    {
        return $this->baronKills;
    }

    /**
     * @param mixed $baronKills
     */
    public function setBaronKills($baronKills): void
    {
        $this->baronKills = $baronKills;
    }

    /**
     * @return mixed
     */
    public function getBountyLevel()
    {
        return $this->bountyLevel;
    }

    /**
     * @param mixed $bountyLevel
     */
    public function setBountyLevel($bountyLevel): void
    {
        $this->bountyLevel = $bountyLevel;
    }

    /**
     * @return mixed
     */
    public function getChampExperience()
    {
        return $this->champExperience;
    }

    /**
     * @param mixed $champExperience
     */
    public function setChampExperience($champExperience): void
    {
        $this->champExperience = $champExperience;
    }

    /**
     * @return mixed
     */
    public function getChampLevel()
    {
        return $this->champLevel;
    }

    /**
     * @param mixed $champLevel
     */
    public function setChampLevel($champLevel): void
    {
        $this->champLevel = $champLevel;
    }

    /**
     * @return mixed
     */
    public function getChampionId()
    {
        return $this->championId;
    }

    /**
     * @param mixed $championId
     */
    public function setChampionId($championId): void
    {
        $this->championId = $championId;
    }

    /**
     * @return mixed
     */
    public function getChampionName()
    {
        return $this->championName;
    }

    /**
     * @param mixed $championName
     */
    public function setChampionName($championName): void
    {
        $this->championName = $championName;
    }

    /**
     * @return mixed
     */
    public function getChampionTransform()
    {
        return $this->championTransform;
    }

    /**
     * @param mixed $championTransform
     */
    public function setChampionTransform($championTransform): void
    {
        $this->championTransform = $championTransform;
    }

    /**
     * @return mixed
     */
    public function getConsumablesPurchased()
    {
        return $this->consumablesPurchased;
    }

    /**
     * @param mixed $consumablesPurchased
     */
    public function setConsumablesPurchased($consumablesPurchased): void
    {
        $this->consumablesPurchased = $consumablesPurchased;
    }

    /**
     * @return mixed
     */
    public function getDamageDealtToBuildings()
    {
        return $this->damageDealtToBuildings;
    }

    /**
     * @param mixed $damageDealtToBuildings
     */
    public function setDamageDealtToBuildings($damageDealtToBuildings): void
    {
        $this->damageDealtToBuildings = $damageDealtToBuildings;
    }

    /**
     * @return mixed
     */
    public function getDamageDealtToObjectives()
    {
        return $this->damageDealtToObjectives;
    }

    /**
     * @param mixed $damageDealtToObjectives
     */
    public function setDamageDealtToObjectives($damageDealtToObjectives): void
    {
        $this->damageDealtToObjectives = $damageDealtToObjectives;
    }

    /**
     * @return mixed
     */
    public function getDamageDealtToTurrets()
    {
        return $this->damageDealtToTurrets;
    }

    /**
     * @param mixed $damageDealtToTurrets
     */
    public function setDamageDealtToTurrets($damageDealtToTurrets): void
    {
        $this->damageDealtToTurrets = $damageDealtToTurrets;
    }

    /**
     * @return mixed
     */
    public function getDamageSelfMitigated()
    {
        return $this->damageSelfMitigated;
    }

    /**
     * @param mixed $damageSelfMitigated
     */
    public function setDamageSelfMitigated($damageSelfMitigated): void
    {
        $this->damageSelfMitigated = $damageSelfMitigated;
    }

    /**
     * @return mixed
     */
    public function getDeaths()
    {
        return $this->deaths;
    }

    /**
     * @param mixed $deaths
     */
    public function setDeaths($deaths): void
    {
        $this->deaths = $deaths;
    }

    /**
     * @return mixed
     */
    public function getDetectorWardsPlaced()
    {
        return $this->detectorWardsPlaced;
    }

    /**
     * @param mixed $detectorWardsPlaced
     */
    public function setDetectorWardsPlaced($detectorWardsPlaced): void
    {
        $this->detectorWardsPlaced = $detectorWardsPlaced;
    }

    /**
     * @return mixed
     */
    public function getDoubleKills()
    {
        return $this->doubleKills;
    }

    /**
     * @param mixed $doubleKills
     */
    public function setDoubleKills($doubleKills): void
    {
        $this->doubleKills = $doubleKills;
    }

    /**
     * @return mixed
     */
    public function getDragonKills()
    {
        return $this->dragonKills;
    }

    /**
     * @param mixed $dragonKills
     */
    public function setDragonKills($dragonKills): void
    {
        $this->dragonKills = $dragonKills;
    }

    /**
     * @return mixed
     */
    public function getFirstBloodAssist()
    {
        return $this->firstBloodAssist;
    }

    /**
     * @param mixed $firstBloodAssist
     */
    public function setFirstBloodAssist($firstBloodAssist): void
    {
        $this->firstBloodAssist = $firstBloodAssist;
    }

    /**
     * @return mixed
     */
    public function getFirstBloodKill()
    {
        return $this->firstBloodKill;
    }

    /**
     * @param mixed $firstBloodKill
     */
    public function setFirstBloodKill($firstBloodKill): void
    {
        $this->firstBloodKill = $firstBloodKill;
    }

    /**
     * @return mixed
     */
    public function getFirstTowerAssist()
    {
        return $this->firstTowerAssist;
    }

    /**
     * @param mixed $firstTowerAssist
     */
    public function setFirstTowerAssist($firstTowerAssist): void
    {
        $this->firstTowerAssist = $firstTowerAssist;
    }

    /**
     * @return mixed
     */
    public function getFirstTowerKill(): bool
    {
        return $this->firstTowerKill;
    }

    /**
     * @param mixed $firstTowerKill
     */
    public function setFirstTowerKill($firstTowerKill): void
    {
        $this->firstTowerKill = !($firstTowerKill === false);
    }

    /**
     * @return mixed
     */
    public function getGameEndedInEarlySurrender()
    {
        return $this->gameEndedInEarlySurrender;
    }

    /**
     * @param mixed $gameEndedInEarlySurrender
     */
    public function setGameEndedInEarlySurrender($gameEndedInEarlySurrender): void
    {
        $this->gameEndedInEarlySurrender = $gameEndedInEarlySurrender;
    }

    /**
     * @return mixed
     */
    public function getGameEndedInSurrender()
    {
        return $this->gameEndedInSurrender;
    }

    /**
     * @param mixed $gameEndedInSurrender
     */
    public function setGameEndedInSurrender($gameEndedInSurrender): void
    {
        $this->gameEndedInSurrender = $gameEndedInSurrender;
    }

    /**
     * @return mixed
     */
    public function getGoldEarned()
    {
        return $this->goldEarned;
    }

    /**
     * @param mixed $goldEarned
     */
    public function setGoldEarned($goldEarned): void
    {
        $this->goldEarned = $goldEarned;
    }

    /**
     * @return mixed
     */
    public function getGoldSpent()
    {
        return $this->goldSpent;
    }

    /**
     * @param mixed $goldSpent
     */
    public function setGoldSpent($goldSpent): void
    {
        $this->goldSpent = $goldSpent;
    }

    /**
     * @return mixed
     */
    public function getIndividualPosition()
    {
        return $this->individualPosition;
    }

    /**
     * @param mixed $individualPosition
     */
    public function setIndividualPosition($individualPosition): void
    {
        $this->individualPosition = $individualPosition;
    }

    /**
     * @return mixed
     */
    public function getTeamPosition()
    {
        return $this->teamPosition;
    }

    /**
     * @param mixed $teamPosition
     */
    public function setTeamPosition($teamPosition): void
    {
        $this->teamPosition = $teamPosition;
    }

    /**
     * @return mixed
     */
    public function getInhibitorKills()
    {
        return $this->inhibitorKills;
    }

    /**
     * @param mixed $inhibitorKills
     */
    public function setInhibitorKills($inhibitorKills): void
    {
        $this->inhibitorKills = $inhibitorKills;
    }

    /**
     * @return mixed
     */
    public function getInhibitorTakedowns()
    {
        return $this->inhibitorTakedowns;
    }

    /**
     * @param mixed $inhibitorTakedowns
     */
    public function setInhibitorTakedowns($inhibitorTakedowns): void
    {
        $this->inhibitorTakedowns = $inhibitorTakedowns;
    }

    /**
     * @return mixed
     */
    public function getInhibitorsLost()
    {
        return $this->inhibitorsLost;
    }

    /**
     * @param mixed $inhibitorsLost
     */
    public function setInhibitorsLost($inhibitorsLost): void
    {
        $this->inhibitorsLost = $inhibitorsLost;
    }

    /**
     * @return mixed
     */
    public function getItem0()
    {
        return $this->item0;
    }

    /**
     * @param mixed $item0
     */
    public function setItem0($item0): void
    {
        $this->item0 = $item0;
    }

    /**
     * @return mixed
     */
    public function getItem1()
    {
        return $this->item1;
    }

    /**
     * @param mixed $item1
     */
    public function setItem1($item1): void
    {
        $this->item1 = $item1;
    }

    /**
     * @return mixed
     */
    public function getItem2()
    {
        return $this->item2;
    }

    /**
     * @param mixed $item2
     */
    public function setItem2($item2): void
    {
        $this->item2 = $item2;
    }

    /**
     * @return mixed
     */
    public function getItem3()
    {
        return $this->item3;
    }

    /**
     * @param mixed $item3
     */
    public function setItem3($item3): void
    {
        $this->item3 = $item3;
    }

    /**
     * @return mixed
     */
    public function getItem4()
    {
        return $this->item4;
    }

    /**
     * @param mixed $item4
     */
    public function setItem4($item4): void
    {
        $this->item4 = $item4;
    }

    /**
     * @return mixed
     */
    public function getItem5()
    {
        return $this->item5;
    }

    /**
     * @param mixed $item5
     */
    public function setItem5($item5): void
    {
        $this->item5 = $item5;
    }

    /**
     * @return mixed
     */
    public function getItem6()
    {
        return $this->item6;
    }

    /**
     * @param mixed $item6
     */
    public function setItem6($item6): void
    {
        $this->item6 = $item6;
    }

    /**
     * @return mixed
     */
    public function getItemsPurchased()
    {
        return $this->itemsPurchased;
    }

    /**
     * @param mixed $itemsPurchased
     */
    public function setItemsPurchased($itemsPurchased): void
    {
        $this->itemsPurchased = $itemsPurchased;
    }

    /**
     * @return mixed
     */
    public function getKillingSprees()
    {
        return $this->killingSprees;
    }

    /**
     * @param mixed $killingSprees
     */
    public function setKillingSprees($killingSprees): void
    {
        $this->killingSprees = $killingSprees;
    }

    /**
     * @return mixed
     */
    public function getKills()
    {
        return $this->kills;
    }

    /**
     * @param mixed $kills
     */
    public function setKills($kills): void
    {
        $this->kills = $kills;
    }

    /**
     * @return mixed
     */
    public function getLane()
    {
        return $this->lane;
    }

    /**
     * @param mixed $lane
     */
    public function setLane($lane): void
    {
        $this->lane = $lane;
    }

    /**
     * @return mixed
     */
    public function getLargestCriticalStrike()
    {
        return $this->largestCriticalStrike;
    }

    /**
     * @param mixed $largestCriticalStrike
     */
    public function setLargestCriticalStrike($largestCriticalStrike): void
    {
        $this->largestCriticalStrike = $largestCriticalStrike;
    }

    /**
     * @return mixed
     */
    public function getLargestKillingSpree()
    {
        return $this->largestKillingSpree;
    }

    /**
     * @param mixed $largestKillingSpree
     */
    public function setLargestKillingSpree($largestKillingSpree): void
    {
        $this->largestKillingSpree = $largestKillingSpree;
    }

    /**
     * @return mixed
     */
    public function getLargestMultiKill()
    {
        return $this->largestMultiKill;
    }

    /**
     * @param mixed $largestMultiKill
     */
    public function setLargestMultiKill($largestMultiKill): void
    {
        $this->largestMultiKill = $largestMultiKill;
    }

    /**
     * @return mixed
     */
    public function getLongestTimeSpentLiving()
    {
        return $this->longestTimeSpentLiving;
    }

    /**
     * @param mixed $longestTimeSpentLiving
     */
    public function setLongestTimeSpentLiving($longestTimeSpentLiving): void
    {
        $this->longestTimeSpentLiving = $longestTimeSpentLiving;
    }

    /**
     * @return mixed
     */
    public function getMagicDamageDealt()
    {
        return $this->magicDamageDealt;
    }

    /**
     * @param mixed $magicDamageDealt
     */
    public function setMagicDamageDealt($magicDamageDealt): void
    {
        $this->magicDamageDealt = $magicDamageDealt;
    }

    /**
     * @return mixed
     */
    public function getMagicDamageDealtToChampions()
    {
        return $this->magicDamageDealtToChampions;
    }

    /**
     * @param mixed $magicDamageDealtToChampions
     */
    public function setMagicDamageDealtToChampions($magicDamageDealtToChampions): void
    {
        $this->magicDamageDealtToChampions = $magicDamageDealtToChampions;
    }

    /**
     * @return mixed
     */
    public function getMagicDamageTaken()
    {
        return $this->magicDamageTaken;
    }

    /**
     * @param mixed $magicDamageTaken
     */
    public function setMagicDamageTaken($magicDamageTaken): void
    {
        $this->magicDamageTaken = $magicDamageTaken;
    }

    /**
     * @return mixed
     */
    public function getNeutralMinionsKilled()
    {
        return $this->neutralMinionsKilled;
    }

    /**
     * @param mixed $neutralMinionsKilled
     */
    public function setNeutralMinionsKilled($neutralMinionsKilled): void
    {
        $this->neutralMinionsKilled = $neutralMinionsKilled;
    }

    /**
     * @return mixed
     */
    public function getNexusKills()
    {
        return $this->nexusKills;
    }

    /**
     * @param mixed $nexusKills
     */
    public function setNexusKills($nexusKills): void
    {
        $this->nexusKills = $nexusKills;
    }

    /**
     * @return mixed
     */
    public function getNexusTakedowns()
    {
        return $this->nexusTakedowns;
    }

    /**
     * @param mixed $nexusTakedowns
     */
    public function setNexusTakedowns($nexusTakedowns): void
    {
        $this->nexusTakedowns = $nexusTakedowns;
    }

    /**
     * @return mixed
     */
    public function getNexusLost()
    {
        return $this->nexusLost;
    }

    /**
     * @param mixed $nexusLost
     */
    public function setNexusLost($nexusLost): void
    {
        $this->nexusLost = $nexusLost;
    }

    /**
     * @return mixed
     */
    public function getObjectivesStolen()
    {
        return $this->objectivesStolen;
    }

    /**
     * @param mixed $objectivesStolen
     */
    public function setObjectivesStolen($objectivesStolen): void
    {
        $this->objectivesStolen = $objectivesStolen;
    }

    /**
     * @return mixed
     */
    public function getObjectivesStolenAssists()
    {
        return $this->objectivesStolenAssists;
    }

    /**
     * @param mixed $objectivesStolenAssists
     */
    public function setObjectivesStolenAssists($objectivesStolenAssists): void
    {
        $this->objectivesStolenAssists = $objectivesStolenAssists;
    }

    /**
     * @return mixed
     */
    public function getParticipantId()
    {
        return $this->participantId;
    }

    /**
     * @param mixed $participantId
     */
    public function setParticipantId($participantId): void
    {
        $this->participantId = $participantId;
    }

    /**
     * @return mixed
     */
    public function getPentaKills()
    {
        return $this->pentaKills;
    }

    /**
     * @param mixed $pentaKills
     */
    public function setPentaKills($pentaKills): void
    {
        $this->pentaKills = $pentaKills;
    }

    /**
     * @return mixed
     */
    public function getPerks()
    {
        return $this->perks;
    }

    /**
     * @param mixed $perks
     */
    public function setPerks($perks): void
    {
//        $perkObject = new Perk();
//        $perkObject->setParticipant($this);
//
//        foreach ($perks['styles'] as $perkStyles) {
//            $perkStyle = new PerkStyle();
//            $perkStyle->setDescription($perkStyles['description']);
//            $perkStyle->setStyle($perkStyles['style']);
//            foreach ($perkStyles['selections'] as $selection) {
//                $perkStyle->addSelection(new PerkStyleSelection($selection));
//            }
//            $perkObject->addStyle($perkStyle);
//
//        }
//        $perkObject->setStatPerks(new PerkStats($perks['statPerks']));

        $this->perks = $perks;
    }

    /**
     * @return mixed
     */
    public function getPhysicalDamageDealt()
    {
        return $this->physicalDamageDealt;
    }

    /**
     * @param mixed $physicalDamageDealt
     */
    public function setPhysicalDamageDealt($physicalDamageDealt): void
    {
        $this->physicalDamageDealt = $physicalDamageDealt;
    }

    /**
     * @return mixed
     */
    public function getPhysicalDamageDealtToChampions()
    {
        return $this->physicalDamageDealtToChampions;
    }

    /**
     * @param mixed $physicalDamageDealtToChampions
     */
    public function setPhysicalDamageDealtToChampions($physicalDamageDealtToChampions): void
    {
        $this->physicalDamageDealtToChampions = $physicalDamageDealtToChampions;
    }

    /**
     * @return mixed
     */
    public function getPhysicalDamageTaken()
    {
        return $this->physicalDamageTaken;
    }

    /**
     * @param mixed $physicalDamageTaken
     */
    public function setPhysicalDamageTaken($physicalDamageTaken): void
    {
        $this->physicalDamageTaken = $physicalDamageTaken;
    }

    /**
     * @return mixed
     */
    public function getProfileIcon()
    {
        return $this->profileIcon;
    }

    /**
     * @param mixed $profileIcon
     */
    public function setProfileIcon($profileIcon): void
    {
        $this->profileIcon = $profileIcon;
    }

    /**
     * @return mixed
     */
    public function getPuuid()
    {
        return $this->puuid;
    }

    /**
     * @param mixed $puuid
     */
    public function setPuuid($puuid): void
    {
        $this->puuid = $puuid;
    }

    /**
     * @return mixed
     */
    public function getQuadraKills()
    {
        return $this->quadraKills;
    }

    /**
     * @param mixed $quadraKills
     */
    public function setQuadraKills($quadraKills): void
    {
        $this->quadraKills = $quadraKills;
    }

    /**
     * @return mixed
     */
    public function getRiotIdName()
    {
        return $this->riotIdName;
    }

    /**
     * @param mixed $riotIdName
     */
    public function setRiotIdName($riotIdName): void
    {
        $this->riotIdName = $riotIdName;
    }

    /**
     * @return mixed
     */
    public function getRiotIdTagline()
    {
        return $this->riotIdTagline;
    }

    /**
     * @param mixed $riotIdTagline
     */
    public function setRiotIdTagline($riotIdTagline): void
    {
        $this->riotIdTagline = $riotIdTagline;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
    }

    /**
     * @return mixed
     */
    public function getSightWardsBoughtInGame()
    {
        return $this->sightWardsBoughtInGame;
    }

    /**
     * @param mixed $sightWardsBoughtInGame
     */
    public function setSightWardsBoughtInGame($sightWardsBoughtInGame): void
    {
        $this->sightWardsBoughtInGame = $sightWardsBoughtInGame;
    }

    /**
     * @return mixed
     */
    public function getSpell1Casts()
    {
        return $this->spell1Casts;
    }

    /**
     * @param mixed $spell1Casts
     */
    public function setSpell1Casts($spell1Casts): void
    {
        $this->spell1Casts = $spell1Casts;
    }

    /**
     * @return mixed
     */
    public function getSpell2Casts()
    {
        return $this->spell2Casts;
    }

    /**
     * @param mixed $spell2Casts
     */
    public function setSpell2Casts($spell2Casts): void
    {
        $this->spell2Casts = $spell2Casts;
    }

    /**
     * @return mixed
     */
    public function getSpell3Casts()
    {
        return $this->spell3Casts;
    }

    /**
     * @param mixed $spell3Casts
     */
    public function setSpell3Casts($spell3Casts): void
    {
        $this->spell3Casts = $spell3Casts;
    }

    /**
     * @return mixed
     */
    public function getSpell4Casts()
    {
        return $this->spell4Casts;
    }

    /**
     * @param mixed $spell4Casts
     */
    public function setSpell4Casts($spell4Casts): void
    {
        $this->spell4Casts = $spell4Casts;
    }

    /**
     * @return mixed
     */
    public function getSummoner1Casts()
    {
        return $this->summoner1Casts;
    }

    /**
     * @param mixed $summoner1Casts
     */
    public function setSummoner1Casts($summoner1Casts): void
    {
        $this->summoner1Casts = $summoner1Casts;
    }

    /**
     * @return mixed
     */
    public function getSummoner1Id()
    {
        return $this->summoner1Id;
    }

    /**
     * @param mixed $summoner1Id
     */
    public function setSummoner1Id($summoner1Id): void
    {
        $this->summoner1Id = $summoner1Id;
    }

    /**
     * @return mixed
     */
    public function getSummoner2Casts()
    {
        return $this->summoner2Casts;
    }

    /**
     * @param mixed $summoner2Casts
     */
    public function setSummoner2Casts($summoner2Casts): void
    {
        $this->summoner2Casts = $summoner2Casts;
    }

    /**
     * @return mixed
     */
    public function getSummoner2Id()
    {
        return $this->summoner2Id;
    }

    /**
     * @param mixed $summoner2Id
     */
    public function setSummoner2Id($summoner2Id): void
    {
        $this->summoner2Id = $summoner2Id;
    }

    /**
     * @return mixed
     */
    public function getSummonerId()
    {
        return $this->summonerId;
    }

    /**
     * @param mixed $summonerId
     */
    public function setSummonerId($summonerId): void
    {
        $this->summonerId = $summonerId;
    }

    /**
     * @return mixed
     */
    public function getSummonerLevel()
    {
        return $this->summonerLevel;
    }

    /**
     * @param mixed $summonerLevel
     */
    public function setSummonerLevel($summonerLevel): void
    {
        $this->summonerLevel = $summonerLevel;
    }

    /**
     * @return mixed
     */
    public function getSummonerName()
    {
        return $this->summonerName;
    }

    /**
     * @param mixed $summonerName
     */
    public function setSummonerName($summonerName): void
    {
        $this->summonerName = $summonerName;
    }

    /**
     * @return mixed
     */
    public function getTeamEarlySurrendered()
    {
        return $this->teamEarlySurrendered;
    }

    /**
     * @param mixed $teamEarlySurrendered
     */
    public function setTeamEarlySurrendered($teamEarlySurrendered): void
    {
        $this->teamEarlySurrendered = $teamEarlySurrendered;
    }

    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @param mixed $teamId
     */
    public function setTeamId($teamId): void
    {
        $this->teamId = $teamId;
    }

    /**
     * @return mixed
     */
    public function getTimeCCingOthers()
    {
        return $this->timeCCingOthers;
    }

    /**
     * @param mixed $timeCCingOthers
     */
    public function setTimeCCingOthers($timeCCingOthers): void
    {
        $this->timeCCingOthers = $timeCCingOthers;
    }

    /**
     * @return mixed
     */
    public function getTimePlayed()
    {
        return $this->timePlayed;
    }

    /**
     * @param mixed $timePlayed
     */
    public function setTimePlayed($timePlayed): void
    {
        $this->timePlayed = $timePlayed;
    }

    /**
     * @return mixed
     */
    public function getTotalDamageDealt()
    {
        return $this->totalDamageDealt;
    }

    /**
     * @param mixed $totalDamageDealt
     */
    public function setTotalDamageDealt($totalDamageDealt): void
    {
        $this->totalDamageDealt = $totalDamageDealt;
    }

    /**
     * @return mixed
     */
    public function getTotalDamageDealtToChampions()
    {
        return $this->totalDamageDealtToChampions;
    }

    /**
     * @param mixed $totalDamageDealtToChampions
     */
    public function setTotalDamageDealtToChampions($totalDamageDealtToChampions): void
    {
        $this->totalDamageDealtToChampions = $totalDamageDealtToChampions;
    }

    /**
     * @return mixed
     */
    public function getTotalDamageShieldedOnTeammates()
    {
        return $this->totalDamageShieldedOnTeammates;
    }

    /**
     * @param mixed $totalDamageShieldedOnTeammates
     */
    public function setTotalDamageShieldedOnTeammates($totalDamageShieldedOnTeammates): void
    {
        $this->totalDamageShieldedOnTeammates = $totalDamageShieldedOnTeammates;
    }

    /**
     * @return mixed
     */
    public function getTotalDamageTaken()
    {
        return $this->totalDamageTaken;
    }

    /**
     * @param mixed $totalDamageTaken
     */
    public function setTotalDamageTaken($totalDamageTaken): void
    {
        $this->totalDamageTaken = $totalDamageTaken;
    }

    /**
     * @return mixed
     */
    public function getTotalHeal()
    {
        return $this->totalHeal;
    }

    /**
     * @param mixed $totalHeal
     */
    public function setTotalHeal($totalHeal): void
    {
        $this->totalHeal = $totalHeal;
    }

    /**
     * @return mixed
     */
    public function getTotalHealsOnTeammates()
    {
        return $this->totalHealsOnTeammates;
    }

    /**
     * @param mixed $totalHealsOnTeammates
     */
    public function setTotalHealsOnTeammates($totalHealsOnTeammates): void
    {
        $this->totalHealsOnTeammates = $totalHealsOnTeammates;
    }

    /**
     * @return mixed
     */
    public function getTotalMinionsKilled()
    {
        return $this->totalMinionsKilled;
    }

    /**
     * @param mixed $totalMinionsKilled
     */
    public function setTotalMinionsKilled($totalMinionsKilled): void
    {
        $this->totalMinionsKilled = $totalMinionsKilled;
    }

    /**
     * @return mixed
     */
    public function getTotalTimeCCDealt()
    {
        return $this->totalTimeCCDealt;
    }

    /**
     * @param mixed $totalTimeCCDealt
     */
    public function setTotalTimeCCDealt($totalTimeCCDealt): void
    {
        $this->totalTimeCCDealt = $totalTimeCCDealt;
    }

    /**
     * @return mixed
     */
    public function getTotalTimeSpentDead()
    {
        return $this->totalTimeSpentDead;
    }

    /**
     * @param mixed $totalTimeSpentDead
     */
    public function setTotalTimeSpentDead($totalTimeSpentDead): void
    {
        $this->totalTimeSpentDead = $totalTimeSpentDead;
    }

    /**
     * @return mixed
     */
    public function getTotalUnitsHealed()
    {
        return $this->totalUnitsHealed;
    }

    /**
     * @param mixed $totalUnitsHealed
     */
    public function setTotalUnitsHealed($totalUnitsHealed): void
    {
        $this->totalUnitsHealed = $totalUnitsHealed;
    }

    /**
     * @return mixed
     */
    public function getTripleKills()
    {
        return $this->tripleKills;
    }

    /**
     * @param mixed $tripleKills
     */
    public function setTripleKills($tripleKills): void
    {
        $this->tripleKills = $tripleKills;
    }

    /**
     * @return mixed
     */
    public function getTrueDamageDealt()
    {
        return $this->trueDamageDealt;
    }

    /**
     * @param mixed $trueDamageDealt
     */
    public function setTrueDamageDealt($trueDamageDealt): void
    {
        $this->trueDamageDealt = $trueDamageDealt;
    }

    /**
     * @return mixed
     */
    public function getTrueDamageDealtToChampions()
    {
        return $this->trueDamageDealtToChampions;
    }

    /**
     * @param mixed $trueDamageDealtToChampions
     */
    public function setTrueDamageDealtToChampions($trueDamageDealtToChampions): void
    {
        $this->trueDamageDealtToChampions = $trueDamageDealtToChampions;
    }

    /**
     * @return mixed
     */
    public function getTrueDamageTaken()
    {
        return $this->trueDamageTaken;
    }

    /**
     * @param mixed $trueDamageTaken
     */
    public function setTrueDamageTaken($trueDamageTaken): void
    {
        $this->trueDamageTaken = $trueDamageTaken;
    }

    /**
     * @return mixed
     */
    public function getTurretKills()
    {
        return $this->turretKills;
    }

    /**
     * @param mixed $turretKills
     */
    public function setTurretKills($turretKills): void
    {
        $this->turretKills = $turretKills;
    }

    /**
     * @return mixed
     */
    public function getTurretTakedowns()
    {
        return $this->turretTakedowns;
    }

    /**
     * @param mixed $turretTakedowns
     */
    public function setTurretTakedowns($turretTakedowns): void
    {
        $this->turretTakedowns = $turretTakedowns;
    }

    /**
     * @return mixed
     */
    public function getTurretsLost()
    {
        return $this->turretsLost;
    }

    /**
     * @param mixed $turretsLost
     */
    public function setTurretsLost($turretsLost): void
    {
        $this->turretsLost = $turretsLost;
    }

    /**
     * @return mixed
     */
    public function getUnrealKills()
    {
        return $this->unrealKills;
    }

    /**
     * @param mixed $unrealKills
     */
    public function setUnrealKills($unrealKills): void
    {
        $this->unrealKills = $unrealKills;
    }

    /**
     * @return mixed
     */
    public function getVisionScore()
    {
        return $this->visionScore;
    }

    /**
     * @param mixed $visionScore
     */
    public function setVisionScore($visionScore): void
    {
        $this->visionScore = $visionScore;
    }

    /**
     * @return mixed
     */
    public function getVisionWardsBoughtInGame()
    {
        return $this->visionWardsBoughtInGame;
    }

    /**
     * @param mixed $visionWardsBoughtInGame
     */
    public function setVisionWardsBoughtInGame($visionWardsBoughtInGame): void
    {
        $this->visionWardsBoughtInGame = $visionWardsBoughtInGame;
    }

    /**
     * @return mixed
     */
    public function getWardsKilled()
    {
        return $this->wardsKilled;
    }

    /**
     * @param mixed $wardsKilled
     */
    public function setWardsKilled($wardsKilled): void
    {
        $this->wardsKilled = $wardsKilled;
    }

    /**
     * @return mixed
     */
    public function getWardsPlaced()
    {
        return $this->wardsPlaced;
    }

    /**
     * @param mixed $wardsPlaced
     */
    public function setWardsPlaced($wardsPlaced): void
    {
        $this->wardsPlaced = $wardsPlaced;
    }

    /**
     * @return mixed
     */
    public function getWin()
    {
        return $this->win;
    }

    /**
     * @param mixed $win
     */
    public function setWin($win): void
    {
        $this->win = !($win === false);
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info): void
    {
        $this->info = $info;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment): void
    {
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getRiotIdGameName(): string
    {
        return $this->riotIdGameName;
    }

    /**
     * @param string $riotIdGameName
     */
    public function setRiotIdGameName(string $riotIdGameName): void
    {
        $this->riotIdGameName = $riotIdGameName;
    }

    public function getTotalAllyJungleMinionsKilled(): ?int
    {
        return $this->totalAllyJungleMinionsKilled;
    }

    public function setTotalAllyJungleMinionsKilled($totalAllyJungleMinionsKilled): void
    {
        $this->totalAllyJungleMinionsKilled = $totalAllyJungleMinionsKilled;
    }

    public function getTotalEnemyJungleMinionsKilled(): ?int
    {
        return $this->totalEnemyJungleMinionsKilled;
    }

    public function setTotalEnemyJungleMinionsKilled($totalEnemyJungleMinionsKilled): void
    {
        $this->totalEnemyJungleMinionsKilled = $totalEnemyJungleMinionsKilled;
    }

    public function getPlacement(): ?int
    {
        return $this->placement;
    }

    public function setPlacement(?int $placement): void
    {
        $this->placement = $placement;
    }

    public function getPlayerAugment1(): ?int
    {
        return $this->playerAugment1;
    }

    public function setPlayerAugment1($playerAugment1): void
    {
        $this->playerAugment1 = $playerAugment1;
    }

    public function getPlayerAugment2(): ?int
    {
        return $this->playerAugment2;
    }

    public function setPlayerAugment2($playerAugment2): void
    {
        $this->playerAugment2 = $playerAugment2;
    }

    public function getPlayerAugment3(): ?int
    {
        return $this->playerAugment3;
    }

    public function setPlayerAugment3($playerAugment3): void
    {
        $this->playerAugment3 = $playerAugment3;
    }

    public function getPlayerAugment4(): ?int
    {
        return $this->playerAugment4;
    }

    public function setPlayerAugment4($playerAugment4): void
    {
        $this->playerAugment4 = $playerAugment4;
    }

    public function getPlayerAugment5(): ?int
    {
        return $this->playerAugment5;
    }

    public function setPlayerAugment5($playerAugment5): void
    {
        $this->playerAugment5 = $playerAugment5;
    }

    public function getPlayerAugment6(): ?int
    {
        return $this->playerAugment6;
    }

    public function setPlayerAugment6($playerAugment6): void
    {
        $this->playerAugment6 = $playerAugment6;
    }

    public function getPlayerSubteamId(): ?int
    {
        return $this->playerSubteamId;
    }

    public function setPlayerSubteamId($playerSubteamId): void
    {
        $this->playerSubteamId = $playerSubteamId;
    }

    public function getSubteamPlacement(): ?int
    {
        return $this->subteamPlacement;
    }

    public function setSubteamPlacement($subteamPlacement): void
    {
        $this->subteamPlacement = $subteamPlacement;
    }
}
