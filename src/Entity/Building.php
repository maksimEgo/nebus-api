<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity]
class Building
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    #[Groups(['organization'])]
    private readonly int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['organization'])]
    private string $address;

    #[ORM\Column(type: 'float')]
    #[Groups(['organization'])]
    private float $latitude;

    #[ORM\Column(type: 'float')]
    #[Groups(['organization'])]
    private float $longitude;

    #[ORM\OneToMany(targetEntity: Organization::class, mappedBy: 'building')]
    #[Groups(['organization'])]
    #[Ignore]
    private Collection $organizations;

    public function __construct()
    {
        $this->organizations = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): void
    {
        $this->latitude = $latitude;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): void
    {
        $this->longitude = $longitude;
    }

    public function getOrganizations(): Collection
    {
        return $this->organizations;
    }

    public function setOrganizations(Collection $organizations): void
    {
        $this->organizations = $organizations;
    }
}