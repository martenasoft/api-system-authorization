<?php

namespace App\Controller;

use App\Service\AccessKeyService;
use App\Service\PermissionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class Authorization extends AbstractController
{
    public function __construct(
        private AccessKeyService $accessKeyService,
        private PermissionService $permissionService
    ) {
    }
    #[Route('/get-access-token', name: 'get_access_token')]
    public function __invoke(): JsonResponse
    {
        $permissions = $this->permissionService->getPermissions();
        $accessToken = $this->accessKeyService->get($this->getUser(), $permissions);
        return $this->json(['accessToken' => $accessToken]);
    }
}
