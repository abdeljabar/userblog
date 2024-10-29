<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[MongoDB\Document]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[MongoDB\Id]
    #[Groups(['user:read', 'user:write'])]
    private ?string $id = null;

    #[MongoDB\Field(type: "string")]
    #[Groups(['user:read', 'user:write', 'user:update'])]
    #[Assert\NotBlank(groups: ['user:write'])]
    #[Assert\Type('string')]
    private string $name;

    #[MongoDB\Field(type: "string")]
    #[Groups(['user:read', 'user:write', 'user:update'])]
    #[Assert\NotBlank(groups: ['user:write'])]
    #[Assert\Email]
    private string $email;

    #[MongoDB\Field(type: "string")]
    private string $password;

    #[Groups(['user:write'])]
    #[Assert\NotBlank(groups: ['user:write'])]
    #[Assert\PasswordStrength(minScore: PasswordStrength::STRENGTH_STRONG)]
    private ?string $plainPassword = null;

    #[MongoDB\Field(type: 'collection')]
    private array $roles = [];

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

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     * @return void
     */
    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * The public representation of the user email address
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // Clear sensitive data if any
    }
}
