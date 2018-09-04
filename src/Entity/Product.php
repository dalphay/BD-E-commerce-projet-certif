<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ToBuy", mappedBy="product", orphanRemoval=true)
     */
    private $toBuys;

    public function __construct()
    {
        $this->toBuys = new ArrayCollection();
    }

    public function getId()
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|ToBuy[]
     */
    public function getToBuys(): Collection
    {
        return $this->toBuys;
    }

    public function addToBuy(ToBuy $toBuy): self
    {
        if (!$this->toBuys->contains($toBuy)) {
            $this->toBuys[] = $toBuy;
            $toBuy->setProduct($this);
        }

        return $this;
    }

    public function removeToBuy(ToBuy $toBuy): self
    {
        if ($this->toBuys->contains($toBuy)) {
            $this->toBuys->removeElement($toBuy);
            // set the owning side to null (unless already changed)
            if ($toBuy->getProduct() === $this) {
                $toBuy->setProduct(null);
            }
        }

        return $this;
    }
}
