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
    const IMAGES_PATH = 'images/products/';
    const MIME_TYPES = ['image/jpeg', 'image/png'];
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

    /**
     * @ORM\Column(type="text")
     */
    private $imageURI;

    public function __construct(String $name = "", String $description = "", Int $price = 0, String $base64Image = "")
    {
        $this->toBuys = new ArrayCollection();
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->imageURI = "";
        if ($base64Image !== "")
        {
            $this->setBase64Image($base64Image);
        }
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

    public function getDescription() : ? string
    {
        return $this->description;
    }

    public function setDescription(string $description) : self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice() : ? int
    {
        return $this->price;
    }

    public function setPrice(int $price) : self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|ToBuy[]
     */
    public function getToBuys() : Collection
    {
        return $this->toBuys;
    }

    public function addToBuy(ToBuy $toBuy) : self
    {
        if (!$this->toBuys->contains($toBuy)) {
            $this->toBuys[] = $toBuy;
            $toBuy->setProduct($this);
        }

        return $this;
    }

    public function removeToBuy(ToBuy $toBuy) : self
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

    public function setImageURI(String $imageURI)
    {
        $this->imageURI = $imageURI;

        return $this;
    }

    public function getImageURI()
    {
        return $this->imageURI;
    }

    public function setBase64Image(String $base64Image)
    {
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $base64Image);
        // we could add validation here with ensuring count( $data ) > 1
        $decodedImage = base64_decode($data[1]);
        // open the output file for writing
        $file = fopen('/tmp/tempImage', 'wb');
        fwrite($file, $decodedImage);
        // clean up the file resource
        fclose($file);

        $imageType = mime_content_type('/tmp/tempImage');
        // if mime type is allowed
        if (in_array($imageType, self::MIME_TYPES)){
            // if an image is already attached to the product ...
            if ($this->imageURI !== "") {
                // ... delete it
                unlink($this->imageURI);
            }
            // generate a unique filename
            $filename = md5(uniqid());
            // get extension e.g png
            preg_match('/.*\/(.*)/', $imageType, $matches);
            $extension = $matches[1];
            // construct file path + name + extension string
            $filePath = self::IMAGES_PATH . $filename . '.' . $extension;
            // move from template to our storage folder
            rename('/tmp/tempImage', $filePath);
            // save imageURI
            $this->imageURI = $filePath;
        }
        return $this;
    }
}
