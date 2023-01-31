<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="info")
 */
class Info
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Game", inversedBy="info", cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;

    /**
     * @ORM\Column(type="bigint")
     */
    private $gameCreation;

    /**
     * @ORM\Column(type="bigint")
     */
    private $gameDuration;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $gameEndTimestamp;

    /**
     * @ORM\Column(type="bigint")
     */
    private $gameUUID;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gameMode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gameName;

    /**
     * @ORM\Column(type="bigint")
     */
    private $gameStartTimestamp;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gameType;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gameVersion;

    /**
     * @ORM\Column(type="integer")
     */
    private $mapId;

    /**
     * @ORM\OneToMany(targetEntity="Participant", mappedBy="gameMetadata", cascade={"persist"})
     */
    private $participants;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $platformId;

    /**
     * @ORM\Column(type="integer")
     */
    private $queueId;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $teams;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tournamentCode;

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
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param mixed $game
     */
    public function setGame($game): void
    {
        $this->game = $game;
    }

    /**
     * @return mixed
     */
    public function getGameCreation()
    {
        return $this->gameCreation;
    }

    /**
     * @param mixed $gameCreation
     */
    public function setGameCreation($gameCreation): void
    {
        $this->gameCreation = $gameCreation;
    }

    /**
     * @return mixed
     */
    public function getGameDuration()
    {
        return $this->gameDuration;
    }

    /**
     * @param mixed $gameDuration
     */
    public function setGameDuration($gameDuration): void
    {
        $this->gameDuration = $gameDuration;
    }

    /**
     * @return mixed
     */
    public function getGameEndTimestamp()
    {
        return $this->gameEndTimestamp;
    }

    /**
     * @param mixed $gameEndTimestamp
     */
    public function setGameEndTimestamp($gameEndTimestamp): void
    {
        $this->gameEndTimestamp = $gameEndTimestamp;
    }

    /**
     * @return mixed
     */
    public function getGameId()
    {
        return $this->gameUUID;
    }

    /**
     * @param mixed $gameId
     */
    public function setGameId($gameId): void
    {
        $this->gameUUID = $gameId;
    }

    /**
     * @return mixed
     */
    public function getGameMode()
    {
        return $this->gameMode;
    }

    /**
     * @param mixed $gameMode
     */
    public function setGameMode($gameMode): void
    {
        $this->gameMode = $gameMode;
    }

    /**
     * @return mixed
     */
    public function getGameName()
    {
        return $this->gameName;
    }

    /**
     * @param mixed $gameName
     */
    public function setGameName($gameName): void
    {
        $this->gameName = $gameName;
    }

    /**
     * @return mixed
     */
    public function getGameStartTimestamp()
    {
        return $this->gameStartTimestamp;
    }

    /**
     * @param mixed $gameStartTimestamp
     */
    public function setGameStartTimestamp($gameStartTimestamp): void
    {
        $this->gameStartTimestamp = $gameStartTimestamp;
    }

    /**
     * @return mixed
     */
    public function getGameType()
    {
        return $this->gameType;
    }

    /**
     * @param mixed $gameType
     */
    public function setGameType($gameType): void
    {
        $this->gameType = $gameType;
    }

    /**
     * @return mixed
     */
    public function getGameVersion()
    {
        return $this->gameVersion;
    }

    /**
     * @param mixed $gameVersion
     */
    public function setGameVersion($gameVersion): void
    {
        $this->gameVersion = $gameVersion;
    }

    /**
     * @return mixed
     */
    public function getMapId()
    {
        return $this->mapId;
    }

    /**
     * @param mixed $mapId
     */
    public function setMapId($mapId): void
    {
        $this->mapId = $mapId;
    }

    /**
     * @return mixed
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param mixed $participants
     */
    public function setParticipants($participants): void
    {
        $this->participants = $participants;
    }

    /**
     * @return mixed
     */
    public function getPlatformId()
    {
        return $this->platformId;
    }

    /**
     * @param mixed $platformId
     */
    public function setPlatformId($platformId): void
    {
        $this->platformId = $platformId;
    }

    /**
     * @return mixed
     */
    public function getQueueId()
    {
        return $this->queueId;
    }

    /**
     * @param mixed $queueId
     */
    public function setQueueId($queueId): void
    {
        $this->queueId = $queueId;
    }

    /**
     * @return mixed
     */
    public function getTeams()
    {
        return $this->teams;
    }

    /**
     * @param mixed $teams
     */
    public function setTeams($teams): void
    {
        $this->teams = $teams;
    }

    /**
     * @return mixed
     */
    public function getTournamentCode()
    {
        return $this->tournamentCode;
    }

    /**
     * @param mixed $tournamentCode
     */
    public function setTournamentCode($tournamentCode): void
    {
        $this->tournamentCode = $tournamentCode;
    }

    public function addParticipants(Participant $participant): void
    {
        $this->participants[] = $participant;
    }
}
