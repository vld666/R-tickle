<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @Gedmo\SoftDeleteable
 */

class Article
{

    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $image;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="articles")
     */
    private ?Category $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $publishedBy;

    /**
     * @ORM\Column(type="boolean")
     */
    private ?bool $visible;

    /**
     * @ORM\OneToMany(targetEntity=FavArticle::class, mappedBy="article", orphanRemoval=true)
     */
    private Collection $favArticles;

    public function __construct()
    {
        $this->favArticles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPublishedBy(): ?User
    {
        return $this->publishedBy;
    }

    public function setPublishedBy(?User $publishedBy): self
    {
        $this->publishedBy = $publishedBy;

        return $this;
    }

    public function getVisible(): ?bool
    {
        return $this->visible;
    }

    public function setVisible(?bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * @return Collection<int, FavArticle>
     */
    public function getFavArticles(): Collection
    {
        return $this->favArticles;
    }

    public function addFavArticle(FavArticle $favArticle): self
    {
        if (!$this->favArticles->contains($favArticle)) {
            $this->favArticles[] = $favArticle;
            $favArticle->setArticle($this);
        }

        return $this;
    }

    public function removeFavArticle(FavArticle $favArticle): self
    {
        if ($this->favArticles->removeElement($favArticle)) {
            // set the owning side to null (unless already changed)
            if ($favArticle->getArticle() === $this) {
                $favArticle->setArticle(null);
            }
        }

        return $this;
    }
}
