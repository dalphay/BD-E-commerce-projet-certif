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
     * @ORM\OneToOne(targetEntity="App\Entity\User", mappedBy="shoppingCart", cascade={"persist", "remove"})
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ToBuy", mappedBy="shoppingCart", orphanRemoval=true)
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
}
