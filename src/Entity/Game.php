<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="game")
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Info", inversedBy="game", cascade={"persist"})
     * @ORM\JoinColumn(name="info_id", referencedColumnName="id")
     */
    private $info;

    /**
     * @ORM\OneToOne(targetEntity="Metadata", inversedBy="game", cascade={"persist"})
     * @ORM\JoinColumn(name="metadata_id", referencedColumnName="id")
     */
    private $metadata;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $backfilled = false;

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
    public function getInfo(): Info
    {
        return $this->info;
    }

    /**
     * @param mixed $info
     */
    public function setInfo($info): void
    {
        $this->info = $info;
        $info->setGame($this);
    }

    /**
     * @return mixed
     */
    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    /**
     * @param mixed $metadata
     */
    public function setMetadata($metadata): void
    {
        $this->metadata = $metadata;
        $metadata->setGame($this);
    }

    public function isBackfilled(): bool
    {
        return $this->backfilled;
    }

    public function setBackfilled(bool $backfilled): void
    {
        $this->backfilled = $backfilled;
    }
}
