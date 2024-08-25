<?php

namespace App\Service\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface;

interface AccessKeyServiceInterface
{
    public function get(UserInterface $user, array $permissions): string;
}
