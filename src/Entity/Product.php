<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="product")
 * @ORM\Entity
 */
class Product
{
    /**
     * @Groups({"product"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", nullable=false, options={"unsigned": true})
     */
    private $id;
    /**
     * @Groups({"product"})
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $dateCreate;
    /**
     * @Groups({"product"})
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateUpdate;
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=255)
     * @Groups({"product"})
     * @ORM\Column(type="string", length=255, nullable=false, unique=true)
     */
    private $name;
    /**
     * @Assert\NotBlank
     * @Assert\Length(min=3, max=5000)
     * @Groups({"product"})
     * @ORM\Column(type="string", length=5000, nullable=false)
     */
    private $description;
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="numeric")
     * @Groups({"product"})
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=false, options={"unsigned": true})
     */
    private $price;
    /**
     * @Assert\Length(max=255)
     * @Groups({"product"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $size;
    /**
     * @Assert\Length(max=255)
     * @Groups({"product"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $composition;
    /**
     * @Assert\Length(max=255)
     * @Groups({"product"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $manufacturer;
    /**
     * @Groups({"product"})
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true, fetch="LAZY")
     * @ORM\JoinColumn(name="id", referencedColumnName="product_id", nullable=false)
     * @OA\Property(type="array", @OA\Items(ref=@Model(type=Photo::class, groups={"product"})))
     *
     * @var Collection
     * @Assert\Count(min=1, max=255)
     */
    private $photos;
    /**
     * @Groups({"product"})
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     */
    private $category;

    public function __construct()
    {
        $this->setDateCreate(new \DateTime());
        $this->setPhotos(new ArrayCollection());
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description): self
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
     *
     * @return $this
     */
    public function setPrice($price): self
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
     *
     * @return $this
     */
    public function setSize($size): self
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
     *
     * @return $this
     */
    public function setComposition($composition): self
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
     *
     * @return $this
     */
    public function setManufacturer($manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @return $this
     */
    public function setPhotos(Collection $photos): self
    {
        $this->photos = $photos;

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
     * @return $this
     */
    public function setCategory(Category $category): self
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
     * @return $this
     */
    public function setDateUpdate(\DateTime $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }
}
