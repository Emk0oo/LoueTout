<?php

namespace App\Entity;

use App\Repository\InstanceSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstanceSettingsRepository::class)]
class InstanceSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $primary_color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secondary_color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tertiary_color = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $accent_color = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getPrimaryColor(): ?string
    {
        return $this->primary_color;
    }

    public function setPrimaryColor(?string $primary_color): static
    {
        $this->primary_color = $primary_color;

        return $this;
    }

    public function getSecondaryColor(): ?string
    {
        return $this->secondary_color;
    }

    public function setSecondaryColor(?string $secondary_color): static
    {
        $this->secondary_color = $secondary_color;

        return $this;
    }

    public function getTertiaryColor(): ?string
    {
        return $this->tertiary_color;
    }

    public function setTertiaryColor(?string $tertiary_color): static
    {
        $this->tertiary_color = $tertiary_color;

        return $this;
    }

    public function getAccentColor(): ?string
    {
        return $this->accent_color;
    }

    public function setAccentColor(?string $accent_color): static
    {
        $this->accent_color = $accent_color;

        return $this;
    }
}
