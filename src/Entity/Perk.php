<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="perk")
 */
class Perk
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="PerkStats", inversedBy="statPerks", cascade={"persist"})
     * @ORM\JoinColumn(name="stat_perks_id", referencedColumnName="id")
     */
    protected $statPerks;

    /**
     * @ORM\OneToMany(targetEntity="PerkStyle", mappedBy="styles", cascade={"persist"})
     */
    protected $styles;

    public function __construct()
    {
        $this->styles = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set statPerks.
     *
     * @param PerkStats $statPerks
     *
     * @return Perk
     */
    public function setStatPerks(PerkStats $statPerks)
    {
        $this->statPerks = $statPerks;

        return $this;
    }

    /**
     * Get statPerks.
     *
     * @return PerkStats
     */
    public function getStatPerks()
    {
        return $this->statPerks;
    }

    /**
     * Add style.
     *
     * @param PerkStyle $style
     *
     * @return Perk
     */
    public function addStyle(PerkStyle $style)
    {
        $style->setPerk($this);
        $this->styles[] = $style;

        return $this;
    }

    /**
     * Remove style.
     *
     * @param PerkStyle $style
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeStyle(PerkStyle $style)
    {
        return $this->styles->removeElement($style);
    }

    /**
     * Get styles.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStyles()
    {
        return $this->styles;
    }
}
