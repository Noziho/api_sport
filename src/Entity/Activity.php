<?php

namespace App\Entity;

use App\Repository\ActivityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ActivityRepository::class)]
class Activity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getActivity'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getActivity'])]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getActivity'])]
    private ?string $duration = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getActivity'])]
    private ?string $distance = null;

    #[ORM\Column]
    #[Groups(['getActivity'])]
    private ?float $calories_burned = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['getActivity'])]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'activities')]
    #[Groups(['getActivity'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getDistance(): ?string
    {
        return $this->distance;
    }

    public function setDistance(string $distance): static
    {
        $this->distance = $distance;

        return $this;
    }

    public function getCaloriesBurned(): ?float
    {
        return $this->calories_burned;
    }

    public function setCaloriesBurned(float $calories_burned): static
    {
        $this->calories_burned = $calories_burned;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
