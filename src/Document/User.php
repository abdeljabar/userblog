<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Attribute\Groups;

#[MongoDB\Document]
class User
{
    #[MongoDB\Id]
    #[Groups(['user:read', 'user:write'])]
    private ?string $id = null;

    #[MongoDB\Field(type: "string")]
    #[Groups(['user:read', 'user:write'])]
    private string $name;

    #[MongoDB\Field(type: "string")]
    #[Groups(['user:read', 'user:write'])]
    private string $email;

    #[MongoDB\Field(type: "string")]
    #[Groups(['user:write'])]
    private string $password;

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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
