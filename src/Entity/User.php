<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=24)
     */
    private $name;
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $surname;
    /**
     * @ORM\Column(type="text")
     */
    private $address;
    /**
     * @ORM\Column(type="integer")
     */
    private $gender;
    /**
     * @ORM\Column(type="text")
     */
    private $email;
    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password; // the password MUST be encoded before setting it !
    /**
     * @ORM\Column(type="string", length=24)
     */
    private $role;
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ShoppingCart", inversedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $shoppingCart;
    // we must give a default value for each constructor params to allow symfony instanciating it.
    public function __construct(String $name = "", String $surname = "", String $address = "", Int $gender = null, String $email = "") {
        $this->name = $name;
        $this->surname = $surname;
        $this->address = $address;
        $this->gender = $gender;
        $this->email = $email;
        $this->role = "ROLE_USER";
        return $this;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getName() : ? string
    {
        return $this->name;
    }
    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }
    public function getSurname() : ? string
    {
        return $this->surname;
    }
    public function setSurname(string $surname) : self
    {
        $this->surname = $surname;
        return $this;
    }
    public function getAddress() : ? string
    {
        return $this->address;
    }
    public function setAddress(string $address) : self
    {
        $this->address = $address;
        return $this;
    }
    public function getGender() : ? int
    {
        return $this->gender;
    }
    public function setGender(int $gender) : self
    {
        $this->gender = $gender;
        return $this;
    }
    public function getEmail() : ? string
    {
        return $this->email;
    }
    public function setEmail(string $email) : self
    {
        $this->email = $email;
        return $this;
    }
    public function getRoles()
    {
        // to work, symfony needs an array of roles which allow us to have multiple roles by users.
        return array($this->role);
    }
    public function setRole(String $role)
    {
        $this->role = $role;
        return $this;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function setPassword($password)
    {
        $this->password = $password;
        
        return $this;
    }
    public function getSalt()
    {
        return null;
    }
    public function getUsername()
    {
        return $this->email;
    }
    public function eraseCredentials()
    {
    }
    // serialize and unSerialize are specifying to php how to write/read session token
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
        ) = unserialize($serialized, array('allowed_classes' => false));
    }
    public function getShoppingCart() : ? ShoppingCart
    {
        return $this->shoppingCart;
    }
    public function setShoppingCart(ShoppingCart $shoppingCart) : self
    {
        $this->shoppingCart = $shoppingCart;
        return $this;
    }
}
