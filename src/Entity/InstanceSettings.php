<?php

namespace App\Entity;

use App\Repository\InstanceSettingsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: InstanceSettingsRepository::class)]
class InstanceSettings
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

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

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Instance $instance = null;

    public function getId(): Uuid
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

    public function getInstance(): ?Instance
    {
        return $this->instance;
    }

    public function setInstance(?Instance $instance): static
    {
        $this->instance = $instance;

        return $this;
    }
}
