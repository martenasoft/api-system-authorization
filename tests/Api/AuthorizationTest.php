<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\DataFixtures\UserRoleFixture;
use App\Service\AccessKeyService;
use App\Service\Interfaces\PermissionServiceInterface;
use App\Service\PermissionService;
use App\Service\UserService;
use App\Tests\Traits\TestHelperTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthorizationTest extends ApiTestCase
{
    use TestHelperTrait;
    public function testGettingAccessToken(): void
    {
        $client = static::createClient();
        $token = $this->getUserToken($client);
        $this->mockServices($client->getContainer());
        $result = $client->request('POST', 'https://authorization.localhost/get-access-token', [

            'headers' => [
                'Content-Type' => 'application/ld+json',
                'Authorization' => sprintf('Bearer %s', $token)
            ],
        ]);

        $token = $result->toArray()['accessToken'];
        $roles = $this->parseToken($client->getContainer(), $token)['USER_ROLE'];
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals($roles, array_keys($this->getPermissions()));
    }

    private function mockServices(ContainerInterface $container)
    {
        $accessService = $this->getAccessKeyService($container);
        $permissionService = $this->getPermissionService($container, $this->getPermissions());
        $container->set(AccessKeyService::class, $accessService);
        $container->set(PermissionService::class, $permissionService);
    }
    private function getMockUserService(ContainerInterface $container): PermissionServiceInterface
    {
        $parameterBag = $container->get(ParameterBagInterface::class);
        $client = $container->get(HttpClientInterface::class);
        $requestStack = $container->get(RequestStack::class);
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $userServiceMocked = new class (
            $parameterBag,
            $client,
            $requestStack,
            $passwordHasher
        ) extends UserService {
            public function getUserId(string $email, string $password): int
            {
                return 1;
            }
        };

        return $userServiceMocked;
    }
}
