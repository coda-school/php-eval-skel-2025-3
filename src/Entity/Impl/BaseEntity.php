<?php

namespace App\Entity\Impl;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use Doctrine\ORM\Mapping\JoinColumn;

#[ORM\MappedSuperclass]
#[ORM\HasLifecycleCallbacks]
abstract class BaseEntity
{
    #[ORM\Column(name: 'created_date', type: Types::DATETIME_MUTABLE, nullable: false, options: ['default' => "CURRENT_TIMESTAMP"])]
    protected DateTime $createdDate;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: true)]
    protected ?User $createdBy = null;

    #[ORM\Column(name: 'updated_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTime $updatedDate = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: true)]
    protected ?User $updatedBy = null;

    #[ORM\Column(name: 'deleted_date', type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?DateTime $deletedDate = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: true)]
    protected ?User $deletedBy = null;

    #[ORM\Column(name: 'is_deleted', type: Types::BOOLEAN, nullable: false, options: ['default' => false])]
    protected bool $isDeleted = false;

    public function __construct()
    {
        $this->createdDate = new DateTime();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (!isset($this->createdDate)) {
            $this->createdDate = new DateTime();
        }

        if ($this->updatedDate === null) {
            $this->updatedDate = new DateTime();
        }
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedDate = new DateTime();
    }

    public function softDelete(?User $deleter = null): self
    {
        $this->isDeleted = true;
        $this->deletedDate = new DateTime();
        $this->deletedBy = $deleter;

        return $this;
    }

    public function recover(): self
    {
        $this->isDeleted = false;
        $this->deletedDate = null;
        $this->deletedBy = null;

        return $this;
    }

    public function getCreatedDate(): DateTime
    {
        return $this->createdDate;
    }

    public function setCreatedDate(DateTime $createdDate): self
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getUpdatedDate(): ?DateTime
    {
        return $this->updatedDate;
    }

    public function setUpdatedDate(?DateTime $updatedDate): self
    {
        $this->updatedDate = $updatedDate;
        return $this;
    }

    public function getUpdatedBy(): ?User
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?User $updatedBy): self
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    public function getDeletedDate(): ?DateTime
    {
        return $this->deletedDate;
    }

    public function setDeletedDate(?DateTime $deletedDate): self
    {
        $this->deletedDate = $deletedDate;
        return $this;
    }

    public function getDeletedBy(): ?User
    {
        return $this->deletedBy;
    }

    public function setDeletedBy(?User $deletedBy): self
    {
        $this->deletedBy = $deletedBy;
        return $this;
    }

    public function isDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;
        if ($isDeleted && $this->deletedDate === null) {
            $this->deletedDate = new DateTime();
        }
        return $this;
    }
}
