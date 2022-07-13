<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Domain\DomainController;
use App\Controller\Domain\MakeWhoisController;
use App\Controller\Domain\SaveDomainController;
use App\Repository\DomainRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DomainRepository::class)]
#[ApiResource()]

class Domain
{
    #[ORM\Id]
    #[ORM\Column(type:"bigint", unique:true)]
    #[ORM\GeneratedValue(strategy:"AUTO")]
    #[Assert\Uuid]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $expiryDate;

    #[ORM\Column(type: 'string', length: 255)]
    private $expiryTime;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $launchTime;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastUpdate;

    #[ORM\Column(type: 'boolean')]
    private $hold;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getExpiryDate(): ?string
    {
        return $this->expiryDate;
    }

    public function setExpiryDate(string $expiryDate): self
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    public function getExpiryTime(): ?string
    {
        return $this->expiryTime;
    }

    public function setExpiryTime(string $expiryTime): self
    {
        $this->expiryTime = $expiryTime;

        return $this;
    }

    public function getLaunchTime(): ?string
    {
        return $this->launchTime;
    }

    public function setLaunchTime(?string $launchTime): self
    {
        $this->launchTime = $launchTime;

        return $this;
    }

    public function getLastUpdate(): ?string
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(string $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function isHold(): ?bool
    {
        return $this->hold;
    }

    public function setHold(bool $hold): self
    {
        $this->hold = $hold;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
