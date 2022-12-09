<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[Groups(['product'])]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', nullable: false, options: ['unsigned' => true])]
    private ?int $id = null;

    #[Groups(['product'])]
    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTime $dateCreate = null;

    #[Groups(['product'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $dateUpdate = null;

    #[Groups(['product'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $name = '';

    #[Groups(['product'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 5000)]
    #[ORM\Column(type: 'string', length: 5000, nullable: false)]
    private string $description = '';

    #[Groups(['product'])]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false, options: ['unsigned' => true])]
    private float $price = 0.0;

    #[Groups(['product'])]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $size = null;

    #[Groups(['product'])]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $composition = null;

    #[Groups(['product'])]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $manufacturer = null;

    /**
     * @var Collection<Photo>
     */
    #[Groups(['product'])]
    #[Assert\Count(min: 1, max: 255)]
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Photo::class, cascade: ['persist'], fetch: 'LAZY', orphanRemoval: true)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'product_id', nullable: false, onDelete: 'CASCADE')]
    private Collection $photos;

    #[Groups(['product'])]
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: false)]
    private ?Category $category = null;

    public function __construct()
    {
        $this->dateCreate = new \DateTime();
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getComposition(): ?string
    {
        return $this->composition;
    }

    public function setComposition(?string $composition): self
    {
        $this->composition = $composition;

        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->manufacturer;
    }

    public function setManufacturer(?string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * @return Collection<Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    /**
     * @param Collection<Photo> $photos
     */
    public function setPhotos(Collection $photos): self
    {
        $this->photos = $photos;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getDateCreate(): ?\DateTime
    {
        return $this->dateCreate;
    }

    public function setDateCreate(\DateTime $dateCreate): self
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    public function getDateUpdate(): ?\DateTime
    {
        return $this->dateUpdate;
    }

    public function setDateUpdate(?\DateTime $dateUpdate): self
    {
        $this->dateUpdate = $dateUpdate;

        return $this;
    }
}
