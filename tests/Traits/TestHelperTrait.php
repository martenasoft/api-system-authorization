<?php

namespace App\Tests\Traits;

use App\DataFixtures\UserRoleFixture;
use App\Service\AccessKeyService;
use App\Service\Interfaces\AccessKeyServiceInterface;
use App\Service\PermissionService;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait TestHelperTrait
{
    public const TEST_USER_ID = 10001;
    private function getUser(): JWTUserInterface
    {
        return new class (['ROLE_USER'], self::TEST_USER_ID) implements JWTUserInterface {
            public function __construct(
                private array $roles,
                private string $id
            ) {
            }
            public function getId(): ?int
            {
                return 10001;
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
        };
    }

    private function getPermissionService(ContainerInterface $container, array $permissions)
    {
        $parameterBag = $container->get(ParameterBagInterface::class);
        $client = $container->get(HttpClientInterface::class);
        $requestStack = $container->get(RequestStack::class);

        $permissionService = new class (
            $parameterBag,
            $client,
            $requestStack,
            $permissions
        ) extends PermissionService {

            public function __construct(
                private ParameterBagInterface $parameterBag,
                private HttpClientInterface $client,
                private RequestStack $requestStack,
                private array $permissions
            ) {
            }
            public function getPermissions(): ?array
            {
                return $this->permissions;
            }
        };

        return $permissionService;
    }
    private function getUserToken($client)
    {
        $jWTTokenManager = $client->getContainer()->get(JWTTokenManagerInterface::class);
        return $jWTTokenManager->createFromPayload($this->getUser(), ['id' => self::TEST_USER_ID]);
    }

    private function getAccessKeyService(ContainerInterface $container): AccessKeyServiceInterface
    {
        $jWTTokenManager = $container->get(JWTTokenManagerInterface::class);
        $service = new  AccessKeyService($jWTTokenManager);
        return $service;
    }

    private function parseToken(ContainerInterface $container, string $token): array
    {
        $jWTTokenManager = $container->get(JWTTokenManagerInterface::class);
        return$jWTTokenManager->parse($token);
    }

    private function getPermissions(): array
    {
        return [
            'test permission 1' => 'USER_ROLE',
            'test permission 2' => 'USER_ROLE',
        ];
    }
}
