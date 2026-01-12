<?php

namespace App\Entity;

use App\Entity\Impl\BaseEntity;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[UniqueEntity(fields: ['email'], message: 'Cet Hibouriel est déjà connu de nos archives.')]
#[UniqueEntity(fields: ['username'], message: 'Cet éponyme est déjà porté par un autre fidèle.')]
class User extends BaseEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 30, unique: true)]
    private ?string $username = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column]
    private ?bool $isVerified;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'followers', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinTable(name: 'user_user')]
    #[ORM\JoinColumn(name: 'user_source', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_target', referencedColumnName: 'id')]
    private Collection $following;

    #[ORM\ManyToMany(targetEntity: self::class, mappedBy: 'following', fetch: 'EXTRA_LAZY')]
    private Collection $followers;

    #[ORM\OneToMany(targetEntity: Tweet::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $tweets;

    public function __construct()
    {
        $this->following = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->tweets = new ArrayCollection();
        $this->roles = ['ROLE_USER'];
        $this->isVerified = false;
        $this->createdDate = new DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);
        return $data;
    }

    public function eraseCredentials(): void {}

    public function getUsername(): ?string { return $this->username; }
    public function setUsername(string $username): static { $this->username = $username; return $this; }

    public function getBio(): ?string { return $this->bio; }
    public function setBio(?string $bio): static { $this->bio = $bio; return $this; }

    public function getAvatar(): ?string { return $this->avatar; }
    public function setAvatar(?string $avatar): static { $this->avatar = $avatar; return $this; }

    public function isVerified(): ?bool { return $this->isVerified; }
    public function setIsVerified(bool $isVerified): static { $this->isVerified = $isVerified; return $this; }

    public function getFollowing(): Collection { return $this->following; }
    public function addFollowing(self $following): static {
        if (!$this->following->contains($following)) { $this->following->add($following); }
        return $this;
    }
    public function removeFollowing(self $following): static { $this->following->removeElement($following); return $this; }

    public function getFollowers(): Collection { return $this->followers; }
    public function addFollower(self $follower): static {
        if (!$this->followers->contains($follower)) { $this->followers->add($follower); $follower->addFollowing($this); }
        return $this;
    }
    public function removeFollower(self $follower): static { if ($this->followers->removeElement($follower)) { $follower->removeFollowing($this); } return $this; }

    public function getTweets(): Collection { return $this->tweets; }
    public function addTweet(Tweet $tweet): static {
        if (!$this->tweets->contains($tweet)) { $this->tweets->add($tweet); $tweet->setAuthor($this); }
        return $this;
    }
    public function removeTweet(Tweet $tweet): static {
        if ($this->tweets->removeElement($tweet)) { if ($tweet->getAuthor() === $this) { $tweet->setAuthor(null); } }
        return $this;
    }
}
