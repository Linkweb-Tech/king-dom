<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Domain\DomainController;
use App\Controller\Domain\MakeWhoisController;
use App\Repository\DomainRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DomainRepository::class)]
#[ApiResource(itemOperations: [
    'get',
    'whois_domain' => [
        'method' => 'GET',
        'path' => '/domain/{id}/whois',
        'controller' => MakeWhoisController::class,
    ],
])]
class Domain
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $expiryDate;

    #[ORM\Column(type: 'string', length: 255)]
    private $expiryTime;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $launchTime;

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
}
