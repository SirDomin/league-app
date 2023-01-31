<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="perk_stats")
 */
class PerkStats
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $defense;

    /**
     * @ORM\Column(type="integer")
     */
    private $flex;

    /**
     * @ORM\Column(type="integer")
     */
    private $offense;

    public function __construct($data)
    {
        $this->defense = $data['defense'];
        $this->flex = $data['flex'];
        $this->offense = $data['offense'];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDefense(): ?int
    {
        return $this->defense;
    }

    public function setDefense(int $defense): self
    {
        $this->defense = $defense;

        return $this;
    }

    public function getFlex(): ?int
    {
        return $this->flex;
    }

    public function setFlex(int $flex): self
    {
        $this->flex = $flex;

        return $this;
    }

    public function getOffense(): ?int
    {
        return $this->offense;
    }

    public function setOffense(int $offense): self
    {
        $this->offense = $offense;

        return $this;
    }
}
