<?php

namespace App\Service;

use App\Service\Interfaces\AccessKeyServiceInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessKeyService implements AccessKeyServiceInterface
{
    public function __construct(private JWTTokenManagerInterface $JWTTokenManager)
    {
    }
    public function get(UserInterface $user, array $permissions): string
    {
        $result = [];
        foreach ($permissions as $permission => $group) {
            $result[$group][] = $permission;
        }
        return $this->JWTTokenManager->createFromPayload($user, $result);
    }
}
