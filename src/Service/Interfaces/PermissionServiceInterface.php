<?php

namespace App\Service\Interfaces;

use App\Entity\User;

interface PermissionServiceInterface
{
    public function getPermissions(): ?array;
}
