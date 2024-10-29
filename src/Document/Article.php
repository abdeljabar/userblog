<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use DateTimeImmutable;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document]
class Article
{
    #[MongoDB\Id]
    #[Groups(['article:read', 'article:delete'])]
    private ?string $id = null;

    #[MongoDB\Field(type: "string")]
    #[Groups(['article:read', 'article:write', 'article:update'])]
    #[Assert\NotBlank(groups: ['article:write'])]
    #[Assert\Type('string')]
    private string $title;

    #[MongoDB\Field(type: "string")]
    #[Groups(['article:read', 'article:write', 'article:update'])]
    #[Assert\NotBlank(groups: ['article:write'])]
    #[Assert\Type('string')]
    private string $content;

    #[MongoDB\ReferenceOne(targetDocument: User::class)]
    #[Groups(['article:read'])]
    private User $author;

    #[MongoDB\Field(type: "date")]
    #[Groups(['article:read'])]
    private DateTimeImmutable $createdAt;

    #[MongoDB\Field(type: "date")]
    #[Groups(['article:read'])]
    private DateTimeImmutable $updatedAt;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @param User $author
     * @return $this
     */
    public function setAuthor(User $author): self
    {
        $this->author = $author;
        return $this;
    }
}
