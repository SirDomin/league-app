<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="challenge")
 */
class Challenge
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Participant", inversedBy="challenge", cascade={"persist"})
     * @ORM\JoinColumn(name="participant_id", referencedColumnName="id")
     */
    private $participant;

    /**
     * @ORM\Column(type="integer")
     */
    private $assistStreakCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $abilityUses;

    /**
     * @ORM\Column(type="integer")
     */
    private $acesBefore15Minutes;

    /**
     * @ORM\Column(type="float")
     */
    private $alliedJungleMonsterKills;

    /**
     * @ORM\Column(type="integer")
     */
    private $baronTakedowns;

    /**
     * @ORM\Column(type="integer")
     */
    private $blastConeOppositeOpponentCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $bountyGold;

    /**
     * @ORM\Column(type="integer")
     */
    private $buffsStolen;

    /**
     * @ORM\Column(type="integer")
     */
    private $completeSupportQuestInTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $controlWardsPlaced;

    /**
     * @ORM\Column(type="float")
     */
    private $damagePerMinute;

    /**
     * @ORM\Column(type="float")
     */
    private $damageTakenOnTeamPercentage;

    /**
     * @ORM\Column(type="integer")
     */
    private $dancedWithRiftHerald;

    /**
     * @ORM\Column(type="integer")
     */
    private $deathsByEnemyChamps;

    /**
     * @ORM\Column(type="integer")
     */
    private $dodgeSkillShotsSmallWindow;

    /**
     * @ORM\Column(type="integer")
     */
    private $doubleAces;

    /**
     * @ORM\Column(type="integer")
     */
    private $dragonTakedowns;

    /**
     * @ORM\Column(type="integer")
     */
    private $earlyLaningPhaseGoldExpAdvantage;

    /**
     * @ORM\Column(type="float")
     */
    private $effectiveHealAndShielding;

    /**
     * @ORM\Column(type="integer")
     */
    private $elderDragonKillsWithOpposingSoul;

    /**
     * @ORM\Column(type="integer")
     */
    private $elderDragonMultikills;

    /**
     * @ORM\Column(type="integer")
     */
    private $enemyChampionImmobilizations;

    /**
     * @ORM\Column(type="float")
     */
    private $enemyJungleMonsterKills;

    /**
     * @ORM\Column(type="integer")
     */
    private $epicMonsterKillsNearEnemyJungler;

    /**
     * @ORM\Column(type="integer")
     */
    private $epicMonsterKillsWithin30SecondOfSpawn;

    /**
     * @ORM\Column(type="integer")
     */
    private $epicMonsterSteals;

    /**
     * @ORM\Column(type="integer")
     */
    private $epicMonsterStolenWithoutSmite;

    /**
     * @ORM\Column(type="integer")
     */
    private $flawlessAces;

    /**
     * @ORM\Column(type="integer")
     */
    private $fullTeamTakedown;

    /**
     * @ORM\Column(type="float")
     */
    private $gameLength;

    /**
     * @ORM\Column(type="integer")
     */
    private $getTakedownsInAllLanesEarlyJungleAsLaner = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $goldPerMinute = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $hadAfkTeammate = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $hadOpenNexus = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $immobilizeAndKillWithAlly = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $initialBuffCount = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $initialCrabCount = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $jungleCsBefore10Minutes = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $junglerTakedownsNearDamagedEpicMonster = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $kTurretsDestroyedBeforePlatesFall = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $kda = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $killAfterHiddenWithAlly = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $killParticipation = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $killedChampTookFullTeamDamageSurvived = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $killsNearEnemyTurret = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $killsOnOtherLanesEarlyJungleAsLaner = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $killsOnRecentlyHealedByAramPack = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $killsUnderOwnTurret = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $killsWithHelpFromEpicMonster = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $knockEnemyIntoTeamAndKill = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $landSkillShotsEarlyGame = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $laneMinionsFirst10Minutes = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $laningPhaseGoldExpAdvantage = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $legendaryCount = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $lostAnInhibitor = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $maxCsAdvantageOnLaneOpponent = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxKillDeficit = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxLevelLeadLaneOpponent = 0;

    /**
     * @ORM\Column(type="float")
     */
    private $moreEnemyJungleThanOpponent = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $multiKillOneSpell = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $multiTurretRiftHeraldCount = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $multikills = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $multikillsAfterAggressiveFlash = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $outerTurretExecutesBefore10Minutes = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $outnumberedKills = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $outnumberedNexusKill = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $perfectDragonSoulsTaken = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $perfectGame = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $pickKillWithAlly = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $playedChampSelectPosition = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $poroExplosions = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $quickCleanse = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $quickFirstTurret = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $quickSoloKills = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $riftHeraldTakedowns = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $saveAllyFromDeath = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $scuttleCrabKills = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $skillshotsDodged = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $skillshotsHit = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $snowballsHit = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $soloBaronKills = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $soloKills = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $soloTurretsLategame = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $stealthWardsPlaced = 0;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $survivedSingleDigitHpCount;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $survivedThreeImmobilizesInFight;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $takedownOnFirstTurret;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $takedowns;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $takedownsAfterGainingLevelAdvantage;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $takedownsBeforeJungleMinionSpawn;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $takedownsFirstXMinutes;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $takedownsInAlcove;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $takedownsInEnemyFountain;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $teamBaronKills;

    /**
     * @ORM\Column(type="float")
     */
    private $teamDamagePercentage;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $teamElderDragonKills;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $teamRiftHeraldKills;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $threeWardsOneSweeperCount;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $tookLargeDamageSurvived;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $turretPlatesTaken;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $turretTakedowns;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $turretsTakenWithRiftHerald;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $twentyMinionsIn3SecondsCount;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $unseenRecalls;

    /**
     * @ORM\Column(type="float")
     */
    private $visionScoreAdvantageLaneOpponent;

    /**
     * @ORM\Column(type="float")
     */
    private $visionScorePerMinute;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $wardTakedowns;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $wardTakedownsBefore20M;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $wardsGuarded;

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
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * @param mixed $participant
     */
    public function setParticipant($participant): void
    {
        $this->participant = $participant;
    }

    /**
     * @return mixed
     */
    public function get12AssistStreakCount()
    {
        return $this->assistStreakCount;
    }

    /**
     * @param mixed $assistStreakCount
     */
    public function set12AssistStreakCount($assistStreakCount): void
    {
        $this->assistStreakCount = $assistStreakCount;
    }

    /**
     * @return mixed
     */
    public function getAbilityUses()
    {
        return $this->abilityUses;
    }

    /**
     * @param mixed $abilityUses
     */
    public function setAbilityUses($abilityUses): void
    {
        $this->abilityUses = $abilityUses;
    }

    /**
     * @return mixed
     */
    public function getAcesBefore15Minutes()
    {
        return $this->acesBefore15Minutes;
    }

    /**
     * @param mixed $acesBefore15Minutes
     */
    public function setAcesBefore15Minutes($acesBefore15Minutes): void
    {
        $this->acesBefore15Minutes = $acesBefore15Minutes;
    }

    /**
     * @return mixed
     */
    public function getAlliedJungleMonsterKills()
    {
        return $this->alliedJungleMonsterKills;
    }

    /**
     * @param mixed $alliedJungleMonsterKills
     */
    public function setAlliedJungleMonsterKills($alliedJungleMonsterKills): void
    {
        $this->alliedJungleMonsterKills = $alliedJungleMonsterKills;
    }

    /**
     * @return mixed
     */
    public function getBaronTakedowns()
    {
        return $this->baronTakedowns;
    }

    /**
     * @param mixed $baronTakedowns
     */
    public function setBaronTakedowns($baronTakedowns): void
    {
        $this->baronTakedowns = $baronTakedowns;
    }

    /**
     * @return mixed
     */
    public function getBlastConeOppositeOpponentCount()
    {
        return $this->blastConeOppositeOpponentCount;
    }

    /**
     * @param mixed $blastConeOppositeOpponentCount
     */
    public function setBlastConeOppositeOpponentCount($blastConeOppositeOpponentCount): void
    {
        $this->blastConeOppositeOpponentCount = $blastConeOppositeOpponentCount;
    }

    /**
     * @return mixed
     */
    public function getBountyGold()
    {
        return $this->bountyGold;
    }

    /**
     * @param mixed $bountyGold
     */
    public function setBountyGold($bountyGold): void
    {
        $this->bountyGold = $bountyGold;
    }

    /**
     * @return mixed
     */
    public function getBuffsStolen()
    {
        return $this->buffsStolen;
    }

    /**
     * @param mixed $buffsStolen
     */
    public function setBuffsStolen($buffsStolen): void
    {
        $this->buffsStolen = $buffsStolen;
    }

    /**
     * @return mixed
     */
    public function getCompleteSupportQuestInTime()
    {
        return $this->completeSupportQuestInTime;
    }

    /**
     * @param mixed $completeSupportQuestInTime
     */
    public function setCompleteSupportQuestInTime($completeSupportQuestInTime): void
    {
        $this->completeSupportQuestInTime = $completeSupportQuestInTime;
    }

    /**
     * @return mixed
     */
    public function getControlWardsPlaced()
    {
        return $this->controlWardsPlaced;
    }

    /**
     * @param mixed $controlWardsPlaced
     */
    public function setControlWardsPlaced($controlWardsPlaced): void
    {
        $this->controlWardsPlaced = $controlWardsPlaced;
    }

    /**
     * @return mixed
     */
    public function getDamagePerMinute()
    {
        return $this->damagePerMinute;
    }

    /**
     * @param mixed $damagePerMinute
     */
    public function setDamagePerMinute($damagePerMinute): void
    {
        $this->damagePerMinute = $damagePerMinute;
    }

    /**
     * @return mixed
     */
    public function getDamageTakenOnTeamPercentage()
    {
        return $this->damageTakenOnTeamPercentage;
    }

    /**
     * @param mixed $damageTakenOnTeamPercentage
     */
    public function setDamageTakenOnTeamPercentage($damageTakenOnTeamPercentage): void
    {
        $this->damageTakenOnTeamPercentage = $damageTakenOnTeamPercentage;
    }

    /**
     * @return mixed
     */
    public function getDancedWithRiftHerald()
    {
        return $this->dancedWithRiftHerald;
    }

    /**
     * @param mixed $dancedWithRiftHerald
     */
    public function setDancedWithRiftHerald($dancedWithRiftHerald): void
    {
        $this->dancedWithRiftHerald = $dancedWithRiftHerald;
    }

    /**
     * @return mixed
     */
    public function getDeathsByEnemyChamps()
    {
        return $this->deathsByEnemyChamps;
    }

    /**
     * @param mixed $deathsByEnemyChamps
     */
    public function setDeathsByEnemyChamps($deathsByEnemyChamps): void
    {
        $this->deathsByEnemyChamps = $deathsByEnemyChamps;
    }

    /**
     * @return mixed
     */
    public function getDodgeSkillShotsSmallWindow()
    {
        return $this->dodgeSkillShotsSmallWindow;
    }

    /**
     * @param mixed $dodgeSkillShotsSmallWindow
     */
    public function setDodgeSkillShotsSmallWindow($dodgeSkillShotsSmallWindow): void
    {
        $this->dodgeSkillShotsSmallWindow = $dodgeSkillShotsSmallWindow;
    }

    /**
     * @return mixed
     */
    public function getDoubleAces()
    {
        return $this->doubleAces;
    }

    /**
     * @param mixed $doubleAces
     */
    public function setDoubleAces($doubleAces): void
    {
        $this->doubleAces = $doubleAces;
    }

    /**
     * @return mixed
     */
    public function getDragonTakedowns()
    {
        return $this->dragonTakedowns;
    }

    /**
     * @param mixed $dragonTakedowns
     */
    public function setDragonTakedowns($dragonTakedowns): void
    {
        $this->dragonTakedowns = $dragonTakedowns;
    }

    /**
     * @return mixed
     */
    public function getEarlyLaningPhaseGoldExpAdvantage()
    {
        return $this->earlyLaningPhaseGoldExpAdvantage;
    }

    /**
     * @param mixed $earlyLaningPhaseGoldExpAdvantage
     */
    public function setEarlyLaningPhaseGoldExpAdvantage($earlyLaningPhaseGoldExpAdvantage): void
    {
        $this->earlyLaningPhaseGoldExpAdvantage = $earlyLaningPhaseGoldExpAdvantage;
    }

    /**
     * @return mixed
     */
    public function getEffectiveHealAndShielding()
    {
        return $this->effectiveHealAndShielding;
    }

    /**
     * @param mixed $effectiveHealAndShielding
     */
    public function setEffectiveHealAndShielding($effectiveHealAndShielding): void
    {
        $this->effectiveHealAndShielding = $effectiveHealAndShielding;
    }

    /**
     * @return mixed
     */
    public function getElderDragonKillsWithOpposingSoul()
    {
        return $this->elderDragonKillsWithOpposingSoul;
    }

    /**
     * @param mixed $elderDragonKillsWithOpposingSoul
     */
    public function setElderDragonKillsWithOpposingSoul($elderDragonKillsWithOpposingSoul): void
    {
        $this->elderDragonKillsWithOpposingSoul = $elderDragonKillsWithOpposingSoul;
    }

    /**
     * @return mixed
     */
    public function getElderDragonMultikills()
    {
        return $this->elderDragonMultikills;
    }

    /**
     * @param mixed $elderDragonMultikills
     */
    public function setElderDragonMultikills($elderDragonMultikills): void
    {
        $this->elderDragonMultikills = $elderDragonMultikills;
    }

    /**
     * @return mixed
     */
    public function getEnemyChampionImmobilizations()
    {
        return $this->enemyChampionImmobilizations;
    }

    /**
     * @param mixed $enemyChampionImmobilizations
     */
    public function setEnemyChampionImmobilizations($enemyChampionImmobilizations): void
    {
        $this->enemyChampionImmobilizations = $enemyChampionImmobilizations;
    }

    /**
     * @return mixed
     */
    public function getEnemyJungleMonsterKills()
    {
        return $this->enemyJungleMonsterKills;
    }

    /**
     * @param mixed $enemyJungleMonsterKills
     */
    public function setEnemyJungleMonsterKills($enemyJungleMonsterKills): void
    {
        $this->enemyJungleMonsterKills = $enemyJungleMonsterKills;
    }

    /**
     * @return mixed
     */
    public function getEpicMonsterKillsNearEnemyJungler()
    {
        return $this->epicMonsterKillsNearEnemyJungler;
    }

    /**
     * @param mixed $epicMonsterKillsNearEnemyJungler
     */
    public function setEpicMonsterKillsNearEnemyJungler($epicMonsterKillsNearEnemyJungler): void
    {
        $this->epicMonsterKillsNearEnemyJungler = $epicMonsterKillsNearEnemyJungler;
    }

    /**
     * @return mixed
     */
    public function getEpicMonsterKillsWithin30SecondOfSpawn()
    {
        return $this->epicMonsterKillsWithin30SecondOfSpawn;
    }

    /**
     * @param mixed epicMonsterKillsWithin30SecondOfSpawn
     */
    public function setEpicMonsterKillsWithin30SecondsOfSpawn($epicMonsterKillsWithin30SecondOfSpawn): void
    {
        $this->epicMonsterKillsWithin30SecondOfSpawn = $epicMonsterKillsWithin30SecondOfSpawn;
    }

    /**
     * @return int
     */
    public function getEpicMonsterSteals(): int
    {
        return $this->epicMonsterSteals;
    }

    /**
     * @param int $epicMonsterSteals
     */
    public function setEpicMonsterSteals(int $epicMonsterSteals): void
    {
        $this->epicMonsterSteals = $epicMonsterSteals;
    }

    /**
     * @return int
     */
    public function getEpicMonsterStolenWithoutSmite(): int
    {
        return $this->epicMonsterStolenWithoutSmite;
    }

    /**
     * @param int $epicMonsterStolenWithoutSmite
     */
    public function setEpicMonsterStolenWithoutSmite(int $epicMonsterStolenWithoutSmite): void
    {
        $this->epicMonsterStolenWithoutSmite = $epicMonsterStolenWithoutSmite;
    }

    /**
     * @return int
     */
    public function getFlawlessAces(): int
    {
        return $this->flawlessAces;
    }

    /**
     * @param int $flawlessAces
     */
    public function setFlawlessAces(int $flawlessAces): void
    {
        $this->flawlessAces = $flawlessAces;
    }

    /**
     * @return int
     */
    public function getFullTeamTakedown(): int
    {
        return $this->fullTeamTakedown;
    }

    /**
     * @param int $fullTeamTakedown
     */
    public function setFullTeamTakedown(int $fullTeamTakedown): void
    {
        $this->fullTeamTakedown = $fullTeamTakedown;
    }

    /**
     * @return float
     */
    public function getGameLength(): float
    {
        return $this->gameLength;
    }

    /**
     * @param float $gameLength
     */
    public function setGameLength(float $gameLength): void
    {
        $this->gameLength = $gameLength;
    }

    /**
     * @return int
     */
    public function getGetTakedownsInAllLanesEarlyJungleAsLaner(): int
    {
        return $this->getTakedownsInAllLanesEarlyJungleAsLaner;
    }

    /**
     * @param int $getTakedownsInAllLanesEarlyJungleAsLaner
     */
    public function setGetTakedownsInAllLanesEarlyJungleAsLaner(int $getTakedownsInAllLanesEarlyJungleAsLaner): void
    {
        $this->getTakedownsInAllLanesEarlyJungleAsLaner = $getTakedownsInAllLanesEarlyJungleAsLaner;
    }

    /**
     * @return float
     */
    public function getGoldPerMinute(): float
    {
        return $this->goldPerMinute;
    }

    /**
     * @param float $goldPerMinute
     */
    public function setGoldPerMinute(float $goldPerMinute): void
    {
        $this->goldPerMinute = $goldPerMinute;
    }

    /**
     * @return int
     */
    public function getHadAfkTeammate(): int
    {
        return $this->hadAfkTeammate;
    }

    /**
     * @param int $hadAfkTeammate
     */
    public function setHadAfkTeammate(int $hadAfkTeammate): void
    {
        $this->hadAfkTeammate = $hadAfkTeammate;
    }

    /**
     * @return int
     */
    public function getHadOpenNexus(): int
    {
        return $this->hadOpenNexus;
    }

    /**
     * @param int $hadOpenNexus
     */
    public function setHadOpenNexus(int $hadOpenNexus): void
    {
        $this->hadOpenNexus = $hadOpenNexus;
    }

    /**
     * @return int
     */
    public function getImmobilizeAndKillWithAlly(): int
    {
        return $this->immobilizeAndKillWithAlly;
    }

    /**
     * @param int $immobilizeAndKillWithAlly
     */
    public function setImmobilizeAndKillWithAlly(int $immobilizeAndKillWithAlly): void
    {
        $this->immobilizeAndKillWithAlly = $immobilizeAndKillWithAlly;
    }

    /**
     * @return int
     */
    public function getInitialBuffCount(): int
    {
        return $this->initialBuffCount;
    }

    /**
     * @param int $initialBuffCount
     */
    public function setInitialBuffCount(int $initialBuffCount): void
    {
        $this->initialBuffCount = $initialBuffCount;
    }

    /**
     * @return int
     */
    public function getInitialCrabCount(): int
    {
        return $this->initialCrabCount;
    }

    /**
     * @param int $initialCrabCount
     */
    public function setInitialCrabCount(int $initialCrabCount): void
    {
        $this->initialCrabCount = $initialCrabCount;
    }

    /**
     * @return int
     */
    public function getJungleCsBefore10Minutes(): int
    {
        return $this->jungleCsBefore10Minutes;
    }

    /**
     * @param int $jungleCsBefore10Minutes
     */
    public function setJungleCsBefore10Minutes(int $jungleCsBefore10Minutes): void
    {
        $this->jungleCsBefore10Minutes = $jungleCsBefore10Minutes;
    }

    /**
     * @return int
     */
    public function getJunglerTakedownsNearDamagedEpicMonster(): int
    {
        return $this->junglerTakedownsNearDamagedEpicMonster;
    }

    /**
     * @param int $junglerTakedownsNearDamagedEpicMonster
     */
    public function setJunglerTakedownsNearDamagedEpicMonster(int $junglerTakedownsNearDamagedEpicMonster): void
    {
        $this->junglerTakedownsNearDamagedEpicMonster = $junglerTakedownsNearDamagedEpicMonster;
    }

    /**
     * @return int
     */
    public function getKTurretsDestroyedBeforePlatesFall(): int
    {
        return $this->kTurretsDestroyedBeforePlatesFall;
    }

    /**
     * @param int $kTurretsDestroyedBeforePlatesFall
     */
    public function setKTurretsDestroyedBeforePlatesFall(int $kTurretsDestroyedBeforePlatesFall): void
    {
        $this->kTurretsDestroyedBeforePlatesFall = $kTurretsDestroyedBeforePlatesFall;
    }

    /**
     * @return float
     */
    public function getKda(): float
    {
        return $this->kda;
    }

    /**
     * @param float $kda
     */
    public function setKda(float $kda): void
    {
        $this->kda = $kda;
    }

    /**
     * @return int
     */
    public function getKillAfterHiddenWithAlly(): int
    {
        return $this->killAfterHiddenWithAlly;
    }

    /**
     * @param int $killAfterHiddenWithAlly
     */
    public function setKillAfterHiddenWithAlly(int $killAfterHiddenWithAlly): void
    {
        $this->killAfterHiddenWithAlly = $killAfterHiddenWithAlly;
    }

    /**
     * @return float
     */
    public function getKillParticipation(): float
    {
        return $this->killParticipation;
    }

    /**
     * @param float $killParticipation
     */
    public function setKillParticipation(float $killParticipation): void
    {
        $this->killParticipation = $killParticipation;
    }

    /**
     * @return int
     */
    public function getKilledChampTookFullTeamDamageSurvived(): int
    {
        return $this->killedChampTookFullTeamDamageSurvived;
    }

    /**
     * @param int killedChampTookFullTeamDamageSurvived
     */
    public function setKilledChampTookFullTeamDamageSurvived(int $killedChampTookFullTeamDamageSurvived): void
    {
        $this->killedChampTookFullTeamDamageSurvived = $killedChampTookFullTeamDamageSurvived;
    }

    /**
     * @return int
     */
    public function getKillsNearEnemyTurret(): int
    {
        return $this->killsNearEnemyTurret;
    }

    /**
     * @param int $killsNearEnemyTurret
     */
    public function setKillsNearEnemyTurret(int $killsNearEnemyTurret): void
    {
        $this->killsNearEnemyTurret = $killsNearEnemyTurret;
    }

    /**
     * @return int
     */
    public function getKillsOnOtherLanesEarlyJungleAsLaner(): int
    {
        return $this->killsOnOtherLanesEarlyJungleAsLaner;
    }

    /**
     * @param int $killsOnOtherLanesEarlyJungleAsLaner
     */
    public function setKillsOnOtherLanesEarlyJungleAsLaner(int $killsOnOtherLanesEarlyJungleAsLaner): void
    {
        $this->killsOnOtherLanesEarlyJungleAsLaner = $killsOnOtherLanesEarlyJungleAsLaner;
    }

    /**
     * @return int
     */
    public function getKillsOnRecentlyHealedByAramPack(): int
    {
        return $this->killsOnRecentlyHealedByAramPack;
    }

    /**
     * @param int $killsOnRecentlyHealedByAramPack
     */
    public function setKillsOnRecentlyHealedByAramPack(int $killsOnRecentlyHealedByAramPack): void
    {
        $this->killsOnRecentlyHealedByAramPack = $killsOnRecentlyHealedByAramPack;
    }

    /**
     * @return int
     */
    public function getKillsUnderOwnTurret(): int
    {
        return $this->killsUnderOwnTurret;
    }

    /**
     * @param int $killsUnderOwnTurret
     */
    public function setKillsUnderOwnTurret(int $killsUnderOwnTurret): void
    {
        $this->killsUnderOwnTurret = $killsUnderOwnTurret;
    }

    /**
     * @return int
     */
    public function getKillsWithHelpFromEpicMonster(): int
    {
        return $this->killsWithHelpFromEpicMonster;
    }

    /**
     * @param int $killsWithHelpFromEpicMonster
     */
    public function setKillsWithHelpFromEpicMonster(int $killsWithHelpFromEpicMonster): void
    {
        $this->killsWithHelpFromEpicMonster = $killsWithHelpFromEpicMonster;
    }

    /**
     * @return int
     */
    public function getKnockEnemyIntoTeamAndKill(): int
    {
        return $this->knockEnemyIntoTeamAndKill;
    }

    /**
     * @param int $knockEnemyIntoTeamAndKill
     */
    public function setKnockEnemyIntoTeamAndKill(int $knockEnemyIntoTeamAndKill): void
    {
        $this->knockEnemyIntoTeamAndKill = $knockEnemyIntoTeamAndKill;
    }

    /**
     * @return mixed
     */
    public function getLandSkillShotsEarlyGame()
    {
        return $this->landSkillShotsEarlyGame;
    }

    /**
     * @param mixed $landSkillShotsEarlyGame
     */
    public function setLandSkillShotsEarlyGame($landSkillShotsEarlyGame): void
    {
        $this->landSkillShotsEarlyGame = $landSkillShotsEarlyGame;
    }

    /**
     * @return mixed
     */
    public function getLaneMinionsFirst10Minutes()
    {
        return $this->laneMinionsFirst10Minutes;
    }

    /**
     * @param mixed $laneMinionsFirst10Minutes
     */
    public function setLaneMinionsFirst10Minutes($laneMinionsFirst10Minutes): void
    {
        $this->laneMinionsFirst10Minutes = $laneMinionsFirst10Minutes;
    }

    /**
     * @return mixed
     */
    public function getLaningPhaseGoldExpAdvantage()
    {
        return $this->laningPhaseGoldExpAdvantage;
    }

    /**
     * @param mixed $laningPhaseGoldExpAdvantage
     */
    public function setLaningPhaseGoldExpAdvantage($laningPhaseGoldExpAdvantage): void
    {
        $this->laningPhaseGoldExpAdvantage = $laningPhaseGoldExpAdvantage;
    }

    /**
     * @return mixed
     */
    public function getLegendaryCount()
    {
        return $this->legendaryCount;
    }

    /**
     * @param mixed $legendaryCount
     */
    public function setLegendaryCount($legendaryCount): void
    {
        $this->legendaryCount = $legendaryCount;
    }

    /**
     * @return mixed
     */
    public function getLostAnInhibitor()
    {
        return $this->lostAnInhibitor;
    }

    /**
     * @param mixed $lostAnInhibitor
     */
    public function setLostAnInhibitor($lostAnInhibitor): void
    {
        $this->lostAnInhibitor = $lostAnInhibitor;
    }

    /**
     * @return mixed
     */
    public function getMaxCsAdvantageOnLaneOpponent()
    {
        return $this->maxCsAdvantageOnLaneOpponent;
    }

    /**
     * @param mixed $maxCsAdvantageOnLaneOpponent
     */
    public function setMaxCsAdvantageOnLaneOpponent($maxCsAdvantageOnLaneOpponent): void
    {
        $this->maxCsAdvantageOnLaneOpponent = $maxCsAdvantageOnLaneOpponent;
    }

    /**
     * @return mixed
     */
    public function getMaxKillDeficit()
    {
        return $this->maxKillDeficit;
    }

    /**
     * @param mixed $maxKillDeficit
     */
    public function setMaxKillDeficit($maxKillDeficit): void
    {
        $this->maxKillDeficit = $maxKillDeficit;
    }

    /**
     * @return mixed
     */
    public function getMaxLevelLeadLaneOpponent()
    {
        return $this->maxLevelLeadLaneOpponent;
    }

    /**
     * @param mixed $maxLevelLeadLaneOpponent
     */
    public function setMaxLevelLeadLaneOpponent($maxLevelLeadLaneOpponent): void
    {
        $this->maxLevelLeadLaneOpponent = $maxLevelLeadLaneOpponent;
    }

    /**
     * @return mixed
     */
    public function getMoreEnemyJungleThanOpponent()
    {
        return $this->moreEnemyJungleThanOpponent;
    }

    /**
     * @param mixed $moreEnemyJungleThanOpponent
     */
    public function setMoreEnemyJungleThanOpponent($moreEnemyJungleThanOpponent): void
    {
        $this->moreEnemyJungleThanOpponent = $moreEnemyJungleThanOpponent;
    }

    /**
     * @return mixed
     */
    public function getMultiKillOneSpell()
    {
        return $this->multiKillOneSpell;
    }

    /**
     * @param mixed $multiKillOneSpell
     */
    public function setMultiKillOneSpell($multiKillOneSpell): void
    {
        $this->multiKillOneSpell = $multiKillOneSpell;
    }

    /**
     * @return mixed
     */
    public function getMultiTurretRiftHeraldCount()
    {
        return $this->multiTurretRiftHeraldCount;
    }

    /**
     * @param mixed $multiTurretRiftHeraldCount
     */
    public function setMultiTurretRiftHeraldCount($multiTurretRiftHeraldCount): void
    {
        $this->multiTurretRiftHeraldCount = $multiTurretRiftHeraldCount;
    }

    /**
     * @return mixed
     */
    public function getMultikills()
    {
        return $this->multikills;
    }

    /**
     * @param mixed $multikills
     */
    public function setMultikills($multikills): void
    {
        $this->multikills = $multikills;
    }

    /**
     * @return mixed
     */
    public function getMultikillsAfterAggressiveFlash()
    {
        return $this->multikillsAfterAggressiveFlash;
    }

    /**
     * @param mixed $multikillsAfterAggressiveFlash
     */
    public function setMultikillsAfterAggressiveFlash($multikillsAfterAggressiveFlash): void
    {
        $this->multikillsAfterAggressiveFlash = $multikillsAfterAggressiveFlash;
    }

    /**
     * @return mixed
     */
    public function getOuterTurretExecutesBefore10Minutes()
    {
        return $this->outerTurretExecutesBefore10Minutes;
    }

    /**
     * @param mixed $outerTurretExecutesBefore10Minutes
     */
    public function setOuterTurretExecutesBefore10Minutes($outerTurretExecutesBefore10Minutes): void
    {
        $this->outerTurretExecutesBefore10Minutes = $outerTurretExecutesBefore10Minutes;
    }

    /**
     * @return mixed
     */
    public function getOutnumberedKills()
    {
        return $this->outnumberedKills;
    }

    /**
     * @param mixed $outnumberedKills
     */
    public function setOutnumberedKills($outnumberedKills): void
    {
        $this->outnumberedKills = $outnumberedKills;
    }

    /**
     * @return mixed
     */
    public function getOutnumberedNexusKill()
    {
        return $this->outnumberedNexusKill;
    }

    /**
     * @param mixed $outnumberedNexusKill
     */
    public function setOutnumberedNexusKill($outnumberedNexusKill): void
    {
        $this->outnumberedNexusKill = $outnumberedNexusKill;
    }

    /**
     * @return mixed
     */
    public function getPerfectDragonSoulsTaken()
    {
        return $this->perfectDragonSoulsTaken;
    }

    /**
     * @param mixed $perfectDragonSoulsTaken
     */
    public function setPerfectDragonSoulsTaken($perfectDragonSoulsTaken): void
    {
        $this->perfectDragonSoulsTaken = $perfectDragonSoulsTaken;
    }

    /**
     * @return mixed
     */
    public function getPerfectGame()
    {
        return $this->perfectGame;
    }

    /**
     * @param mixed $perfectGame
     */
    public function setPerfectGame($perfectGame): void
    {
        $this->perfectGame = $perfectGame;
    }

    /**
     * @return mixed
     */
    public function getPickKillWithAlly()
    {
        return $this->pickKillWithAlly;
    }

    /**
     * @param mixed $pickKillWithAlly
     */
    public function setPickKillWithAlly($pickKillWithAlly): void
    {
        $this->pickKillWithAlly = $pickKillWithAlly;
    }

    /**
     * @return mixed
     */
    public function getPlayedChampSelectPosition()
    {
        return $this->playedChampSelectPosition;
    }

    /**
     * @param mixed $playedChampSelectPosition
     */
    public function setPlayedChampSelectPosition($playedChampSelectPosition): void
    {
        $this->playedChampSelectPosition = $playedChampSelectPosition;
    }

    /**
     * @return mixed
     */
    public function getPoroExplosions()
    {
        return $this->poroExplosions;
    }

    /**
     * @param mixed $poroExplosions
     */
    public function setPoroExplosions($poroExplosions): void
    {
        $this->poroExplosions = $poroExplosions;
    }

    /**
     * @return mixed
     */
    public function getQuickCleanse()
    {
        return $this->quickCleanse;
    }

    /**
     * @param mixed $quickCleanse
     */
    public function setQuickCleanse($quickCleanse): void
    {
        $this->quickCleanse = $quickCleanse;
    }

    /**
     * @return mixed
     */
    public function getQuickFirstTurret()
    {
        return $this->quickFirstTurret;
    }

    /**
     * @param mixed $quickFirstTurret
     */
    public function setQuickFirstTurret($quickFirstTurret): void
    {
        $this->quickFirstTurret = $quickFirstTurret;
    }

    /**
     * @return mixed
     */
    public function getQuickSoloKills()
    {
        return $this->quickSoloKills;
    }

    /**
     * @param mixed $quickSoloKills
     */
    public function setQuickSoloKills($quickSoloKills): void
    {
        $this->quickSoloKills = $quickSoloKills;
    }

    /**
     * @return int
     */
    public function getRiftHeraldTakedowns(): int
    {
        return $this->riftHeraldTakedowns;
    }

    /**
     * @param int $riftHeraldTakedowns
     */
    public function setRiftHeraldTakedowns(int $riftHeraldTakedowns): void
    {
        $this->riftHeraldTakedowns = $riftHeraldTakedowns;
    }

    /**
     * @return int
     */
    public function getSaveAllyFromDeath(): int
    {
        return $this->saveAllyFromDeath;
    }

    /**
     * @param int $saveAllyFromDeath
     */
    public function setSaveAllyFromDeath(int $saveAllyFromDeath): void
    {
        $this->saveAllyFromDeath = $saveAllyFromDeath;
    }

    /**
     * @return int
     */
    public function getScuttleCrabKills(): int
    {
        return $this->scuttleCrabKills;
    }

    /**
     * @param int $scuttleCrabKills
     */
    public function setScuttleCrabKills(int $scuttleCrabKills): void
    {
        $this->scuttleCrabKills = $scuttleCrabKills;
    }

    /**
     * @return int
     */
    public function getSkillshotsDodged(): int
    {
        return $this->skillshotsDodged;
    }

    /**
     * @param int $skillshotsDodged
     */
    public function setSkillshotsDodged(int $skillshotsDodged): void
    {
        $this->skillshotsDodged = $skillshotsDodged;
    }

    /**
     * @return int
     */
    public function getSkillshotsHit(): int
    {
        return $this->skillshotsHit;
    }

    /**
     * @param int $skillshotsHit
     */
    public function setSkillshotsHit(int $skillshotsHit): void
    {
        $this->skillshotsHit = $skillshotsHit;
    }

    /**
     * @return int
     */
    public function getSnowballsHit(): int
    {
        return $this->snowballsHit;
    }

    /**
     * @param int $snowballsHit
     */
    public function setSnowballsHit(int $snowballsHit): void
    {
        $this->snowballsHit = $snowballsHit;
    }

    /**
     * @return int
     */
    public function getSoloBaronKills(): int
    {
        return $this->soloBaronKills;
    }

    /**
     * @param int $soloBaronKills
     */
    public function setSoloBaronKills(int $soloBaronKills): void
    {
        $this->soloBaronKills = $soloBaronKills;
    }

    /**
     * @return int
     */
    public function getSoloKills(): int
    {
        return $this->soloKills;
    }

    /**
     * @param int $soloKills
     */
    public function setSoloKills(int $soloKills): void
    {
        $this->soloKills = $soloKills;
    }

    /**
     * @return int
     */
    public function getSoloTurretsLategame(): int
    {
        return $this->soloTurretsLategame;
    }

    /**
     * @param int $soloTurretsLategame
     */
    public function setSoloTurretsLategame(int $soloTurretsLategame): void
    {
        $this->soloTurretsLategame = $soloTurretsLategame;
    }

    /**
     * @return int
     */
    public function getStealthWardsPlaced(): int
    {
        return $this->stealthWardsPlaced;
    }

    /**
     * @param int $stealthWardsPlaced
     */
    public function setStealthWardsPlaced(int $stealthWardsPlaced): void
    {
        $this->stealthWardsPlaced = $stealthWardsPlaced;
    }

    /**
     * @return int
     */
    public function getSurvivedSingleDigitHpCount(): int
    {
        return $this->survivedSingleDigitHpCount;
    }

    /**
     * @param int $survivedSingleDigitHpCount
     */
    public function setSurvivedSingleDigitHpCount(int $survivedSingleDigitHpCount): void
    {
        $this->survivedSingleDigitHpCount = $survivedSingleDigitHpCount;
    }

    /**
     * @return int
     */
    public function getSurvivedThreeImmobilizesInFight(): int
    {
        return $this->survivedThreeImmobilizesInFight;
    }

    /**
     * @param int $survivedThreeImmobilizesInFight
     */
    public function setSurvivedThreeImmobilizesInFight(int $survivedThreeImmobilizesInFight): void
    {
        $this->survivedThreeImmobilizesInFight = $survivedThreeImmobilizesInFight;
    }

    /**
     * @return int
     */
    public function getTakedownOnFirstTurret(): int
    {
        return $this->takedownOnFirstTurret;
    }

    /**
     * @param int $takedownOnFirstTurret
     */
    public function setTakedownOnFirstTurret(int $takedownOnFirstTurret): void
    {
        $this->takedownOnFirstTurret = $takedownOnFirstTurret;
    }

    /**
     * @return int
     */
    public function getTakedowns(): int
    {
        return $this->takedowns;
    }

    /**
     * @param int $takedowns
     */
    public function setTakedowns(int $takedowns): void
    {
        $this->takedowns = $takedowns;
    }

    /**
     * @return int
     */
    public function getTakedownsAfterGainingLevelAdvantage(): int
    {
        return $this->takedownsAfterGainingLevelAdvantage;
    }

    /**
     * @param int $takedownsAfterGainingLevelAdvantage
     */
    public function setTakedownsAfterGainingLevelAdvantage(int $takedownsAfterGainingLevelAdvantage): void
    {
        $this->takedownsAfterGainingLevelAdvantage = $takedownsAfterGainingLevelAdvantage;
    }

    /**
     * @return int
     */
    public function getTakedownsBeforeJungleMinionSpawn(): int
    {
        return $this->takedownsBeforeJungleMinionSpawn;
    }

    /**
     * @param int $takedownsBeforeJungleMinionSpawn
     */
    public function setTakedownsBeforeJungleMinionSpawn(int $takedownsBeforeJungleMinionSpawn): void
    {
        $this->takedownsBeforeJungleMinionSpawn = $takedownsBeforeJungleMinionSpawn;
    }

    /**
     * @return int
     */
    public function getTakedownsFirstXMinutes(): int
    {
        return $this->takedownsFirstXMinutes;
    }

    /**
     * @param int $takedownsFirstXMinutes
     */
    public function setTakedownsFirstXMinutes(int $takedownsFirstXMinutes): void
    {
        $this->takedownsFirstXMinutes = $takedownsFirstXMinutes;
    }

    /**
     * @return int
     */
    public function getTakedownsInAlcove(): int
    {
        return $this->takedownsInAlcove;
    }

    /**
     * @param int $takedownsInAlcove
     */
    public function setTakedownsInAlcove(int $takedownsInAlcove): void
    {
        $this->takedownsInAlcove = $takedownsInAlcove;
    }

    /**
     * @return int
     */
    public function getTakedownsInEnemyFountain(): int
    {
        return $this->takedownsInEnemyFountain;
    }

    /**
     * @param int $takedownsInEnemyFountain
     */
    public function setTakedownsInEnemyFountain(int $takedownsInEnemyFountain): void
    {
        $this->takedownsInEnemyFountain = $takedownsInEnemyFountain;
    }

    /**
     * @return int
     */
    public function getTeamBaronKills(): int
    {
        return $this->teamBaronKills;
    }

    /**
     * @param int $teamBaronKills
     */
    public function setTeamBaronKills(int $teamBaronKills): void
    {
        $this->teamBaronKills = $teamBaronKills;
    }

    /**
     * @return mixed
     */
    public function getTeamDamagePercentage()
    {
        return $this->teamDamagePercentage;
    }

    /**
     * @param mixed $teamDamagePercentage
     */
    public function setTeamDamagePercentage($teamDamagePercentage): void
    {
        $this->teamDamagePercentage = $teamDamagePercentage;
    }

    /**
     * @return int
     */
    public function getTeamElderDragonKills(): int
    {
        return $this->teamElderDragonKills;
    }

    /**
     * @param int $teamElderDragonKills
     */
    public function setTeamElderDragonKills(int $teamElderDragonKills): void
    {
        $this->teamElderDragonKills = $teamElderDragonKills;
    }

    /**
     * @return int
     */
    public function getTeamRiftHeraldKills(): int
    {
        return $this->teamRiftHeraldKills;
    }

    /**
     * @param int $teamRiftHeraldKills
     */
    public function setTeamRiftHeraldKills(int $teamRiftHeraldKills): void
    {
        $this->teamRiftHeraldKills = $teamRiftHeraldKills;
    }

    /**
     * @return int
     */
    public function getThreeWardsOneSweeperCount(): int
    {
        return $this->threeWardsOneSweeperCount;
    }

    /**
     * @param int $threeWardsOneSweeperCount
     */
    public function setThreeWardsOneSweeperCount(int $threeWardsOneSweeperCount): void
    {
        $this->threeWardsOneSweeperCount = $threeWardsOneSweeperCount;
    }

    /**
     * @return int
     */
    public function getTookLargeDamageSurvived(): int
    {
        return $this->tookLargeDamageSurvived;
    }

    /**
     * @param int $tookLargeDamageSurvived
     */
    public function setTookLargeDamageSurvived(int $tookLargeDamageSurvived): void
    {
        $this->tookLargeDamageSurvived = $tookLargeDamageSurvived;
    }

    /**
     * @return int
     */
    public function getTurretPlatesTaken(): int
    {
        return $this->turretPlatesTaken;
    }

    /**
     * @param int $turretPlatesTaken
     */
    public function setTurretPlatesTaken(int $turretPlatesTaken): void
    {
        $this->turretPlatesTaken = $turretPlatesTaken;
    }

    /**
     * @return int
     */
    public function getTurretTakedowns(): int
    {
        return $this->turretTakedowns;
    }

    /**
     * @param int $turretTakedowns
     */
    public function setTurretTakedowns(int $turretTakedowns): void
    {
        $this->turretTakedowns = $turretTakedowns;
    }

    /**
     * @return int
     */
    public function getTurretsTakenWithRiftHerald(): int
    {
        return $this->turretsTakenWithRiftHerald;
    }

    /**
     * @param int $turretsTakenWithRiftHerald
     */
    public function setTurretsTakenWithRiftHerald(int $turretsTakenWithRiftHerald): void
    {
        $this->turretsTakenWithRiftHerald = $turretsTakenWithRiftHerald;
    }

    /**
     * @return int
     */
    public function getTwentyMinionsIn3SecondsCount(): int
    {
        return $this->twentyMinionsIn3SecondsCount;
    }

    /**
     * @param int $twentyMinionsIn3SecondsCount
     */
    public function setTwentyMinionsIn3SecondsCount(int $twentyMinionsIn3SecondsCount): void
    {
        $this->twentyMinionsIn3SecondsCount = $twentyMinionsIn3SecondsCount;
    }

    /**
     * @return int
     */
    public function getUnseenRecalls(): int
    {
        return $this->unseenRecalls;
    }

    /**
     * @param int $unseenRecalls
     */
    public function setUnseenRecalls(int $unseenRecalls): void
    {
        $this->unseenRecalls = $unseenRecalls;
    }

    /**
     * @return mixed
     */
    public function getVisionScoreAdvantageLaneOpponent()
    {
        return $this->visionScoreAdvantageLaneOpponent;
    }

    /**
     * @param mixed $visionScoreAdvantageLaneOpponent
     */
    public function setVisionScoreAdvantageLaneOpponent($visionScoreAdvantageLaneOpponent): void
    {
        $this->visionScoreAdvantageLaneOpponent = $visionScoreAdvantageLaneOpponent;
    }

    /**
     * @return mixed
     */
    public function getVisionScorePerMinute()
    {
        return $this->visionScorePerMinute;
    }

    /**
     * @param mixed $visionScorePerMinute
     */
    public function setVisionScorePerMinute($visionScorePerMinute): void
    {
        $this->visionScorePerMinute = $visionScorePerMinute;
    }

    /**
     * @return int
     */
    public function getWardTakedowns(): int
    {
        return $this->wardTakedowns;
    }

    /**
     * @param int $wardTakedowns
     */
    public function setWardTakedowns(int $wardTakedowns): void
    {
        $this->wardTakedowns = $wardTakedowns;
    }

    /**
     * @return int
     */
    public function getWardTakedownsBefore20M(): int
    {
        return $this->wardTakedownsBefore20M;
    }

    /**
     * @param int $wardTakedownsBefore20M
     */
    public function setWardTakedownsBefore20M(int $wardTakedownsBefore20M): void
    {
        $this->wardTakedownsBefore20M = $wardTakedownsBefore20M;
    }

    /**
     * @return int
     */
    public function getWardsGuarded(): int
    {
        return $this->wardsGuarded;
    }

    /**
     * @param int $wardsGuarded
     */
    public function setWardsGuarded(int $wardsGuarded): void
    {
        $this->wardsGuarded = $wardsGuarded;
    }
}
