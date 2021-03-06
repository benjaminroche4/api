<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Array_;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["get:author:detail", "get:book:list"])]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(["get:author:detail", "get:book:list", "post:book"])]
    #[Assert\Length(
        min: 5,
        max: 50,
        minMessage: 'The name of the book must be at least {{ limit }} characters long',
        maxMessage: 'The name of the book cannot be longer than {{ limit }} characters',
    )]
    private $name;

    #[ORM\Column(type: 'text')]
    #[Groups(["get:author:detail", 'get:book:detail', "post:book"])]
    #[Assert\Length(
        min: 5,
        minMessage: 'The content must be at least {{ limit }} characters long',
    )]
    private $content;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(["get:author:detail", 'get:book:detail'])]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'books', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['get:book:detail', "post:book"])]
    private $author;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'books', cascade: ['persist'])]
    #[Groups(['get:book:detail', "post:book"])]
    private $category;

    public function __construct()
    {
        $this->author = new Author();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

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

}
