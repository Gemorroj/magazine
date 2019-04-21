<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @Groups({"category", "product"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     */
    private $id;
    /**
     * @Groups({"category"})
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateCreate;
    /**
     * @Groups({"category"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateUpdate;
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=255)
     * @Groups({"category"})
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
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
     *
     * @return $this
     */
    public function setId($id): self
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
     *
     * @return $this
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param Collection $products
     *
     * @return $this
     */
    public function setProducts(Collection $products): self
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
     *
     * @return $this
     */
    public function setDateCreate(\DateTime $dateCreate): self
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
     *
     * @return $this
     */
    public function setDateUpdate(\DateTime $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }
}
