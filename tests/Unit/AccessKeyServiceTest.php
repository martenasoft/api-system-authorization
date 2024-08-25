<?php

namespace App\Tests\Unit;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Tests\Traits\TestHelperTrait;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
class AccessKeyServiceTest extends ApiTestCase
{
    use TestHelperTrait;
    public function testGet()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $service = $this->getAccessKeyService($container);
        $token = $service->get($this->getUser(), $this->getPermissions());
        $roles = $this->parseToken($container, $token)['USER_ROLE'];
        $this->assertEquals($roles, array_keys($this->getPermissions()));
    }
}
