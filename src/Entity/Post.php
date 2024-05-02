<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Title;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Title::class, inversedBy: "posts", cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false, name: "title_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?Title $title;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "posts", cascade: ["persist"])]
    #[ORM\JoinColumn(nullable: false, name: "author_id", referencedColumnName: "id", onDelete: "CASCADE")]
    private ?User $user;

    #[ORM\Column(length: 255)]
    private ?string $postInfo = null;

    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: "datetime_immutable")]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?Title
    {
        return $this->title;
    }

    public function setTitle(?Title $title): void
    {
        $this->title = $title;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getPostInfo(): ?string
    {
        return $this->postInfo;
    }

    public function setPostInfo(string $postInfo): static
    {
        $this->postInfo = $postInfo;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
