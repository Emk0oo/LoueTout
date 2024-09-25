<?php

namespace App\Entity;

use App\Repository\InstanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: InstanceRepository::class)]
class Instance
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sql_db_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sql_user_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sql_db_pass = null;

    #[ORM\Column]
    private ?bool $db_created = false;

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

    public function getSqlDbName(): ?string
    {
        return $this->sql_db_name;
    }

    public function setSqlDbName(string $sql_db_name): static
    {
        $this->sql_db_name = $sql_db_name;

        return $this;
    }

    public function getSqlUserName(): ?string
    {
        return $this->sql_user_name;
    }

    public function setSqlUserName(string $sql_user_name): static
    {
        $this->sql_user_name = $sql_user_name;

        return $this;
    }

    public function getSqlDbPass(): ?string
    {
        return $this->sql_db_pass;
    }

    public function setSqlDbPass(string $sql_db_pass): static
    {
        $this->sql_db_pass = $sql_db_pass;

        return $this;
    }

    public function isDbCreated(): ?bool
    {
        return $this->db_created;
    }

    public function setDbCreated(bool $db_created): static
    {
        $this->db_created = $db_created;

        return $this;
    }
}
