<?php

namespace App\Entity;

use App\Repository\MainCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MainCategoryRepository::class)]
class MainCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["mainCategory:read", "mainCategory:write"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["mainCategory:read", "mainCategory:write"])]
    private ?string $name = null;

    #[ORM\Column(length: 20, nullable: true)]
    #[Groups(["mainCategory:read", "mainCategory:write"])]
    private ?string $color = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'main', orphanRemoval: true)]
    #[Groups(["mainCategory:read"])]
    private Collection $categories;

    #[ORM\ManyToOne(inversedBy: 'mainCategories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["mainCategory:read"])]
    private ?User $user = null;


    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setMain($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getMain() === $this) {
                $category->setMain(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
