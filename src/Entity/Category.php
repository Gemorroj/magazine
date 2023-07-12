<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
final class Category
{
    #[Groups(['category', 'product'])]
    #[ORM\Id, ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer', nullable: false, options: ['unsigned' => true])]
    private ?int $id = null;

    #[Groups(['category'])]
    #[ORM\Column(type: 'datetime', nullable: false)]
    private ?\DateTime $dateCreate = null;

    #[Groups(['category'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $dateUpdate = null;

    #[Groups(['category'])]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private string $name = '';

    /**
     * @var Collection<Product>
     */
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Product::class, cascade: ['persist'], fetch: 'LAZY', orphanRemoval: true)]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'category_id', nullable: true, onDelete: 'CASCADE')]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->dateCreate = new \DateTime();
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

    /**
     * @return Collection<Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @param Collection<Product> $products
     */
    public function setProducts(Collection $products): self
    {
        $this->products = $products;

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
