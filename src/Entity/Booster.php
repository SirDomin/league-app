<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="booster")
 */
class Booster
{
    public const TYPE_BOOSTER = 'booster';
    public const TYPE_PLAYER = 'player';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", options={"default":""})
     */
    private string $summonerName = '';

    /**
     * @ORM\Column(type="string", options={"default":""})
     */
    private string $summonerTag = '';

    /**
     * @ORM\Column(type="string", options={"default":""})
     */
    private string $puuid = '';

    /**
     * @ORM\Column(type="string", options={"default":""})
     */
    private string $region = '';

    /**
     * @ORM\Column(type="integer")
     */
    private int $iconId = 0;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $iconIdToVerify = 0;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $expiresAt = null;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?\DateTimeImmutable $validUntil = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $valid = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $type = null;


    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSummonerName(): string
    {
        return $this->summonerName;
    }

    public function setSummonerName(string $summonerName): void
    {
        $this->summonerName = $summonerName;
    }

    public function getSummonerTag(): string
    {
        return $this->summonerTag;
    }

    public function setSummonerTag(string $summonerTag): void
    {
        $this->summonerTag = $summonerTag;
    }

    public function getPuuid(): string
    {
        return $this->puuid;
    }

    public function setPuuid(string $puuid): void
    {
        $this->puuid = $puuid;
    }

    public function getIconId(): int
    {
        return $this->iconId;
    }

    public function setIconId(int $iconId): void
    {
        $this->iconId = $iconId;
    }

    public function getIconIdToVerify(): ?int
    {
        return $this->iconIdToVerify;
    }

    public function setIconIdToVerify(?int $iconIdToVerify): void
    {
        $this->iconIdToVerify = $iconIdToVerify;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    public function getValidUntil(): ?\DateTimeImmutable
    {
        return $this->validUntil;
    }

    public function setValidUntil(?\DateTimeImmutable $validUntil): void
    {
        $this->validUntil = $validUntil;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }
}
