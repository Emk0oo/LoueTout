<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private Uuid $id;

    #[ORM\Column(length: 255)]
    private ?string $label = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    /**
     * @var Collection<int, ProductImage>
     */
    #[ORM\OneToMany(targetEntity: ProductImage::class, mappedBy: 'product',orphanRemoval: true, cascade: ['persist'])]
    private Collection $images;

    /**
     * @var Collection<int, RentHistory>
     */
    #[ORM\OneToMany(targetEntity: RentHistory::class, mappedBy: 'product')]
    private Collection $rentHistory;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Instance $instance = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $rentBy = null;

    public function __construct()
    {
        $this->images = new ArrayCollection();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    /**
     * @return Collection<int, ProductImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(ProductImage $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(ProductImage $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }


    public function setRentHistory(?RentHistory $rentHistory): static
    {
        $this->rentHistory = $rentHistory;

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

    public function getRentBy(): ?User
    {
        return $this->rentBy;
    }

    public function setRentBy(?User $rentBy): static
    {
        $this->rentBy = $rentBy;

        return $this;
    }

    /**
     * Get the first image of the product or null if there is no image
     *
     * @return ProductImage|null
     */
    public function getFirstImage(): ?ProductImage
    {
        return count($this->images) > 0 ? $this->images->first() : null;
    }

    /**
     * Check if the product can be rented
     *
     * @return boolean
     */
    public function canBeRented(): bool
    {
        // $canBeRented = true;

        // foreach($this->rentHistory as $history) {
        //     if($history->getStartAt() > new \DateTime()) {
        //         $canBeRented = false;
        //         break;
        //     }
        // }

        // return $canBeRented;
        return true;
    }

}
