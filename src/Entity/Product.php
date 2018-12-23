<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
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
     * @SWG\Property(type="array", @SWG\Items(ref=@Model(type=Photo::class, groups={"product"})))
     *
     * @var Collection
     * @Assert\Count(min=1)
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
     * @return $this
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPhotos()
    {
        return $this->photos;
    }

    /**
     * @param ArrayCollection $photos
     *
     * @return $this
     */
    public function setPhotos(ArrayCollection $photos)
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
     * @param Category $category
     *
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
     *
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
     *
     * @return $this
     */
    public function setDateUpdate(\DateTime $dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }
}
