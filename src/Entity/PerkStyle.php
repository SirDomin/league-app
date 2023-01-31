<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="perk_style")
 */
class PerkStyle
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="PerkStyleSelection", mappedBy="perkStyle", cascade={"persist"})
     */
    private $selections;

    /**
     * @ORM\ManyToOne(targetEntity="Perk", inversedBy="styles")
     * @ORM\JoinColumn(name="perk_id", referencedColumnName="id")
     */
    private $perk;

    /**
     * @ORM\Column(type="integer")
     */
    private $style;

    public function __construct()
    {
        $this->selections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function setPerk(Perk $perk): void
    {
        $this->perk = $perk;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function addSelection(PerkStyleSelection $selection): self
    {
        if (!$this->selections->contains($selection)) {
            $this->selections[] = $selection;
            $selection->setPerkStyle($this);
        }

        return $this;
    }

    public function removeSelection(PerkStyleSelection $selection): self
    {
        if ($this->selections->contains($selection)) {
            $this->selections->removeElement($selection);
            // set the owning side to null (unless already changed)
            if ($selection->getPerkStyle() === $this) {
                $selection->setPerkStyle(null);
            }
        }

        return $this;
    }

    public function getSelections(): ?ArrayCollection
    {
        return $this->selections;
    }

    public function setStyle(int $style): self
    {
        $this->style = $style;

        return $this;
    }

    public function getStyle(): ?int
    {
        return $this->style;
    }
}
