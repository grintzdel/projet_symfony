<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(targetEntity: Articles::class, mappedBy: 'categories')]
    private Collection $id_articles;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $thumbnail = null;

    #[ORM\OneToMany(targetEntity: Articles::class, mappedBy: 'id_categories')]
    private Collection $articles;

    public function __construct()
    {
        $this->id_articles = new ArrayCollection();
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getIdArticles(): Collection
    {
        return $this->id_articles;
    }

    public function addIdArticle(Articles $idArticle): static
    {
        if (!$this->id_articles->contains($idArticle)) {
            $this->id_articles->add($idArticle);
            $idArticle->setCategories($this);
        }

        return $this;
    }

    public function removeIdArticle(Articles $idArticle): static
    {
        if ($this->id_articles->removeElement($idArticle)) {
            // set the owning side to null (unless already changed)
            if ($idArticle->getCategories() === $this) {
                $idArticle->setCategories(null);
            }
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return Collection<int, Articles>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Articles $article): static
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setIdCategories($this);
        }

        return $this;
    }

    public function removeArticle(Articles $article): static
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getIdCategories() === $this) {
                $article->setIdCategories(null);
            }
        }

        return $this;
    }
}
