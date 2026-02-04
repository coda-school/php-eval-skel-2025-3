<?php

namespace App\Entity;

use App\Entity\Impl\BaseEntity;
use App\Repository\TweetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TweetRepository::class)]
class Tweet extends BaseEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 280)]
    private ?string $content = null;

    #[ORM\Column(length: 280, nullable: true)]
    private ?string $imageFilename = null;

    #[ORM\Column(options: ['default'=>0])]
    private ?int $viewsCount = 0;

    #[ORM\Column(options: ['default'=>0])]
    private ?int $likesCount = 0;

    #[ORM\ManyToOne(inversedBy: 'tweets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(options: ['default'=>0])]
    private ?int $retweetCount = 0;

    #[ORM\Column(options: ['default'=>0])]
    private ?int $replyCount = 0;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $parentTweet = null;

    #[ORM\OneToMany(mappedBy: 'tweet', cascade: ['remove'],  targetEntity: Like::class)]
    private Collection $likes;

    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }

    public function setImageFilename(?string $imageFilename): static
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }

    public function getViewsCount(): ?int
    {
        return $this->viewsCount;
    }

    public function setViewsCount(int $viewsCount): static
    {
        $this->viewsCount = $viewsCount;

        return $this;
    }

    public function getLikesCount(): ?int
    {
        return $this->likesCount;
    }

    public function setLikesCount(int $likesCount): static
    {
        $this->likesCount = $likesCount;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getRetweetCount(): ?int
    {
        return $this->retweetCount;
    }

    public function setRetweetCount(int $retweetCount): static
    {
        $this->retweetCount = $retweetCount;

        return $this;
    }

    public function getReplyCount(): ?int
    {
        return $this->replyCount;
    }

    public function setReplyCount(int $replyCount): static
    {
        $this->replyCount = $replyCount;

        return $this;
    }

    public function getParentTweet(): ?self
    {
        return $this->parentTweet;
    }

    public function setParentTweet(?self $parentTweet): static
    {
        $this->parentTweet = $parentTweet;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setTweet($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            if ($like->getTweet() === $this) {
                $like->setTweet(null);
            }
        }

        return $this;
    }

    public function isLikedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        foreach ($this->likes as $like) {
            if ($like->getUser() === $user) {
                return true;
            }
        }

        return false;
    }

    public function incrementViews(): self {
        $this->viewsCount++;
        return $this;
    }

    public function __toString(): string
    {
        return $this->getContent() ?: 'Tweet #' . $this->getId();
    }

}
