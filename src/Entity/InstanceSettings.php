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
    private ?string $key = null;

    #[ORM\Column(length: 510)]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'instanceSettings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Instance $instance = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

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
