<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @MongoDB\Document(collection="products")
 */
class Product
{
    /**
     * @Groups({"products", "product"})
     * @MongoDB\Id
     */
    private $id;
    /**
     * @Groups({"products", "product"})
     * @MongoDB\Field(type="date")
     */
    private $dateCreate;
    /**
     * @Groups({"products", "product"})
     * @MongoDB\Field(type="date")
     */
    private $dateUpdate;
    /**
     * @Groups({"products", "product"})
     * @MongoDB\Field(type="string")
     */
    private $name;
    /**
     * @Groups({"products", "product"})
     * @MongoDB\Field(type="string")
     */
    private $description;
    /**
     * @Groups({"products", "product"})
     * @MongoDB\Field(type="float")
     */
    private $price;
    /**
     * @Groups({"products", "product"})
     * @MongoDB\Field(type="string")
     */
    private $size;
    /**
     * @Groups({"products", "product"})
     * @MongoDB\Field(type="string")
     */
    private $composition;
    /**
     * @Groups({"products", "product"})
     * @MongoDB\Field(type="string")
     */
    private $manufacturer;
    /**
     * @Groups({"products", "product"})
     * @MongoDB\Field(type="string")
     * @see https://github.com/j0k3r/php-imgur-api-client
     */
    private $preview;
    /**
     * @Groups({"product"})
     * @MongoDB\Field(type="collection")
     * @see @see https://github.com/j0k3r/php-imgur-api-client
     */
    private $photos = [];
    /**
     * @MongoDB\ReferenceOne(targetDocument="Category", inversedBy="products")
     */
    private $category;

    public function __construct()
    {
        $this->setDateCreate(new \DateTime());
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param string $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return string
     */
    public function getComposition()
    {
        return $this->composition;
    }

    /**
     * @param string $composition
     * @return $this
     */
    public function setComposition($composition)
    {
        $this->composition = $composition;
        return $this;
    }

    /**
     * @return string
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param string $manufacturer
     * @return $this
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }

    /**
     * @return string
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * @param string $preview
     * @return $this
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param string[] $photos
     * @return $this
     */
    public function setPhotos(array $photos)
    {
        $this->photos = $photos;
        return $this;
    }

    /**
     * @param string $photo
     * @return $this
     */
    public function addPhoto($photo)
    {
        $this->photos[] = $photo;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreate()
    {
        return $this->dateCreate;
    }

    /**
     * @param \DateTime $dateCreate
     * @return $this
     */
    public function setDateCreate(\DateTime $dateCreate)
    {
        $this->dateCreate = $dateCreate;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateUpdate()
    {
        return $this->dateUpdate;
    }

    /**
     * @param \DateTime $dateUpdate
     * @return $this
     */
    public function setDateUpdate(\DateTime $dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;
        return $this;
    }
}
