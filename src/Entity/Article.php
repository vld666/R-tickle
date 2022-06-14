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
    private $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="articles")
     */
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="articles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $publishedBy;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visible;

    /**
     * @ORM\OneToMany(targetEntity=FavArticle::class, mappedBy="article", orphanRemoval=true)
     */
    private $favArticles;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPaid;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $price = 0;

    /**
     * @ORM\OneToMany(targetEntity=PaidArticles::class, mappedBy="article")
     */
    private $article;
    /**
     * @var ArrayCollection
     */
    private $user;

    /**
     * @ORM\Column(type="integer")
     */
    private $views;

    public function __construct()
    {
        $this->favArticles = new ArrayCollection();
        $this->user = new ArrayCollection();
        $this->article = new ArrayCollection();
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

    public function isIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, PaidArticles>
     */
    public function getArticle(): Collection
    {
        return $this->article;
    }

    public function addArticle(PaidArticles $article): self
    {
        if (!$this->article->contains($article)) {
            $this->article[] = $article;
            $article->setArticle($this);
        }

        return $this;
    }

    public function removeArticle(PaidArticles $article): self
    {
        if ($this->article->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getArticle() === $this) {
                $article->setArticle(null);
            }
        }

        return $this;
    }

    public function getViews(): ?int
    {
        return $this->views;
    }

    public function setViews(int $views): self
    {
        $this->views = $views;

        return $this;
    }
}
