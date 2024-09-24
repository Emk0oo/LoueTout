<?php

namespace App\Entity;

use App\Repository\RentHistoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentHistoryRepository::class)]
class RentHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'rentHistory')]
    private Collection $product;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'rentHistory')]
    private Collection $rent_by;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $started_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $ended_at = null;

    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->rent_by = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->product->contains($product)) {
            $this->product->add($product);
            $product->setRentHistory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getRentHistory() === $this) {
                $product->setRentHistory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getRentBy(): Collection
    {
        return $this->rent_by;
    }

    public function addRentBy(User $rentBy): static
    {
        if (!$this->rent_by->contains($rentBy)) {
            $this->rent_by->add($rentBy);
            $rentBy->setRentHistory($this);
        }

        return $this;
    }

    public function removeRentBy(User $rentBy): static
    {
        if ($this->rent_by->removeElement($rentBy)) {
            // set the owning side to null (unless already changed)
            if ($rentBy->getRentHistory() === $this) {
                $rentBy->setRentHistory(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeImmutable
    {
        return $this->started_at;
    }

    public function setStartedAt(\DateTimeImmutable $started_at): static
    {
        $this->started_at = $started_at;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeImmutable
    {
        return $this->ended_at;
    }

    public function setEndedAt(\DateTimeImmutable $ended_at): static
    {
        $this->ended_at = $ended_at;

        return $this;
    }
}
