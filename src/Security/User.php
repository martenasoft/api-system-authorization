<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

final class User implements JWTUserInterface
{
    public function __construct(
        private array $roles,
        private string $id
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public static function createFromPayload($username, array $payload)
    {
        return new self(
            $payload['roles'], // Added by default
            $payload['id']  // Custom
        );
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return 'id';
    }
}

