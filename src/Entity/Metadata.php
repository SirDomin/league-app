<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="metadata")
 */
class Metadata
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Game", inversedBy="metadata", cascade={"persist"})
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id")
     */
    private $game;

    /**
     * @ORM\Column(type="string")
     */
    private $dataVersion;

    /**
     * @ORM\Column(type="string")
     */
    private $matchId;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $participants;

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
    public function getDataVersion()
    {
        return $this->dataVersion;
    }

    /**
     * @param mixed $dataVersion
     */
    public function setDataVersion($dataVersion): void
    {
        $this->dataVersion = $dataVersion;
    }

    /**
     * @return mixed
     */
    public function getMatchId()
    {
        return $this->matchId;
    }

    /**
     * @param mixed $matchId
     */
    public function setMatchId($matchId): void
    {
        $this->matchId = $matchId;
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

    public function addParticipants(Participant $participant): void
    {
        $this->participants[] = $participant;
    }
}
