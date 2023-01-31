<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="perks")
 */
class PerkStyleSelection
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
    private int $perk;

    /**
     * @ORM\Column(type="integer")
     */
    private int $var1;

    /**
     * @ORM\Column(type="integer")
     */
    private int $var2;

    /**
     * @ORM\Column(type="integer")
     */
    private int $var3;

    /**
     * @ORM\ManyToOne (targetEntity="PerkStyle", inversedBy="perk_style_selection")
     * @ORM\JoinColumn(name="perk_style_id", referencedColumnName="id")
     */
    private $perkStyle;

    public function __construct($data)
    {
        $this->setPerk($data['perk']);
        $this->setVar1($data['var1']);
        $this->setVar2($data['var2']);
        $this->setVar3($data['var3']);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getPerkStyle()
    {
        return $this->perkStyle;
    }

    /**
     * @param mixed $perkStyle
     */
    public function setPerkStyle(PerkStyle $perkStyle): void
    {
        $this->perkStyle = $perkStyle;
    }

    /**
     * @return int
     */
    public function getPerk(): int
    {
        return $this->perk;
    }

    /**
     * @param int $perk
     */
    public function setPerk(int $perk): void
    {
        $this->perk = $perk;
    }

    /**
     * @return int
     */
    public function getVar1(): int
    {
        return $this->var1;
    }

    /**
     * @param int $var1
     */
    public function setVar1(int $var1): void
    {
        $this->var1 = $var1;
    }

    /**
     * @return int
     */
    public function getVar2(): int
    {
        return $this->var2;
    }

    /**
     * @param int $var2
     */
    public function setVar2(int $var2): void
    {
        $this->var2 = $var2;
    }

    /**
     * @return int
     */
    public function getVar3(): int
    {
        return $this->var3;
    }

    /**
     * @param int $var3
     */
    public function setVar3(int $var3): void
    {
        $this->var3 = $var3;
    }
}
