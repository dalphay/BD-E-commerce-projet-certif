<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ShoppingCartRepository")
 */
class ShoppingCart
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="shoppingCart")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ToBuy", mappedBy="shoppingCart", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $toBuys;

    public function __construct()
    {
        $this->toBuys = new ArrayCollection();
        $this->total = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        // set the owning side of the relation if necessary
        if ($this !== $user->getShoppingCart()) {
            $user->setShoppingCart($this);
        }

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
            $toBuy->setShoppingCart($this);
        }

        return $this;
    }

    public function removeToBuy(ToBuy $toBuy): self
    {
        if ($this->toBuys->contains($toBuy)) {
            $this->toBuys->removeElement($toBuy);
            // set the owning side to null (unless already changed)
            if ($toBuy->getShoppingCart() === $this) {
                $toBuy->setShoppingCart(null);
            }
        }

        return $this;
    }

    public function addOrIncrementToBuy(Product $product, Int $qty)
    {
        //  search for already existing toBuy for thos product
        $match = false;
        foreach ($this->getToBuys() as $value) {
            if ($value->getProduct()->getId() == $product->getId()) {
                $value->setQty($value->getQty() + $qty);
                $match = true;
            }
        }

        // if no toBuy are found, creating a new one
        if (!$match) {
            $toBuy = new ToBuy();
            $toBuy->setProduct($product);
            $toBuy->setQty($qty);
            $this->addToBuy($toBuy);
        }

        return $this;
    }

    public function computeTotal()
    {
        $total = 0;
        foreach ($this->getToBuys() as $value) {
            $total += $value->getQty() * $value->getProduct()->getPrice();
        }
        $this->setTotal($total);

        return $this;
    }
}
