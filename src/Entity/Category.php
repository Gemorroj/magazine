<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="category")
 * @ORM\Entity
 */
class Category
{
    /**
     * @Groups({"categories", "product", "products"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer", nullable=false, options={"unsigned":true})
     */
    private $id;
    /**
     * @Groups({"categories"})
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateCreate;
    /**
     * @Groups({"categories"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateUpdate;
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     * @Groups({"categories"})
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;
    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="category", cascade={"persist", "remove"}, orphanRemoval=true, fetch="LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="category_id", nullable=true)
     */
    private $products;

    public function __construct()
    {
        $this->setProducts(new ArrayCollection());
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
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param ArrayCollection $products
     * @return $this
     */
    public function setProducts(ArrayCollection $products)
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
