<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;

#[ORM\Entity]
class Organization
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    #[Groups(['organization'])]
    private readonly int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['organization'])]
    private string $name;

    #[ORM\Column(type: 'json')]
    #[Groups(['organization'])]
    private array $phoneNumbers;

    #[ORM\ManyToOne(targetEntity: Building::class, inversedBy: 'organizations')]
    #[Groups(['organization'])]
    #[MaxDepth(3)]
    private Building $building;

    #[ORM\ManyToMany(targetEntity: Activity::class), Groups(['organization']), MaxDepth(3)]
    private Collection $activity;

    public function __construct()
    {
        $this->activity = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPhoneNumbers(): array
    {
        return $this->phoneNumbers;
    }

    public function setPhoneNumbers(array $phoneNumbers): void
    {
        $this->phoneNumbers = $phoneNumbers;
    }

    public function getBuilding(): Building
    {
        return $this->building;
    }

    public function setBuilding(Building $building): void
    {
        $this->building = $building;
    }

    public function getActivity(): Collection
    {
        return $this->activity;
    }

    public function addActivity(Activity $activity): void
    {
        $this->activity->add($activity);
    }
}