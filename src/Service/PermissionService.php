<?php

namespace App\Service;

use App\Entity\User;
use App\Service\Interfaces\PermissionServiceInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PermissionService implements PermissionServiceInterface
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
        private HttpClientInterface $client,
        private RequestStack $requestStack
    ) {
    }
    public function getPermissions(): ?array
    {
        $token = $this->extractTokenFromRequest();
        $userApiUri = $this->parameterBag->get('api-params')['authentication-api']['uri'] . '/get-user-permissions';

        $response = $this->client->request('POST', $userApiUri, [
            'headers' => [
                'Content-Type' => 'application/ld+json',
                'Authorization' => 'Bearer '.$token
            ],
        ]);

        return $response->toArray();
    }
    private function extractTokenFromRequest(): string
    {
        $authorizationHeader = $this->requestStack->getCurrentRequest()->headers->get('Authorization');

        if (!empty($authorizationHeader)) {
            return str_replace('Bearer ', $authorizationHeader);
        }

        throw new NotFoundHttpException("token not found");
    }
}
