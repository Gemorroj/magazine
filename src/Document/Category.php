<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @MongoDB\Document(collection="categories")
 */
class Category
{
    /**
     * @Groups({"categories"})
     * @MongoDB\Id
     */
    private $id;
    /**
     * @Groups({"categories"})
     * @MongoDB\Field(type="date")
     */
    private $dateCreate;
    /**
     * @Groups({"categories"})
     * @MongoDB\Field(type="date")
     */
    private $dateUpdate;
    /**
     * @Groups({"categories"})
     * @MongoDB\Field(type="string")
     */
    private $name;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Product", mappedBy="category")
     */
    private $products = [];

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
     * @return Product[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Product[] $products
     * @return $this
     */
    public function setProducts(array $products)
    {
        $this->products = $products;
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
