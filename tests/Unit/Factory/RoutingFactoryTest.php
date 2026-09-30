<?php

namespace Kematjaya\URLBundle\Tests\Unit\Factory;

use Kematjaya\URLBundle\Factory\RoutingFactory;
use Kematjaya\URLBundle\Tests\Fixtures\ArrayRoutingSource;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\UserInterface;

class RoutingFactoryTest extends TestCase
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    protected function setUp(): void
    {
        $this->tokenStorage = new TokenStorage();
    }

    private function createFactory(array $settings = [], array $whitelist = [], ?string $basePath = '/admin'): RoutingFactory
    {
        $collection = new RouteCollection();
        $collection->add('homepage', new Route('/'));
        $collection->add('admin_item_index', new Route('/admin/item'));
        $collection->add('admin_item_edit', new Route('/admin/item/{id}/edit'));
        $collection->add('admin_item_print', new Route('/admin/item/{id}/print'));
        $router = $this->createMock(RouterInterface::class);
        $router->method('getRouteCollection')->willReturn($collection);

        $factory = new RoutingFactory(
            new ParameterBag(['url' => ['whitelist' => $whitelist]]),
            $router,
            $this->tokenStorage,
            new ArrayRoutingSource($settings)
        );

        return null === $basePath ? $factory : $factory->setBasePath($basePath);
    }

    private function login(UserInterface $user): void
    {
        $this->tokenStorage->setToken(new UsernamePasswordToken($user, 'main', $user->getRoles()));
    }

    public function testBuildKeepsRoutesUnderBasePathExceptWhitelist(): void
    {
        $routes = $this->createFactory([], ['admin_item_print'])->build();

        $this->assertSame(['admin_item_index', 'admin_item_edit'], $routes->getKeys());
    }

    public function testAnonymousIsDeniedOnConfiguredRoutesOnly(): void
    {
        $routes = $this->createFactory(['admin_item_edit' => ['ROLE_A']])->buildInRoles();

        $this->assertTrue($routes['admin_item_index']);
        $this->assertFalse($routes['admin_item_edit']);
    }

    public function testUserAccessFollowsItsRole(): void
    {
        $this->login(new InMemoryUser('budi', null, ['ROLE_A']));

        $routes = $this->createFactory([
            'admin_item_index' => ['ROLE_A'],
            'admin_item_edit' => ['ROLE_B'],
        ])->buildInRoles();

        $this->assertTrue($routes['admin_item_index']);
        $this->assertFalse($routes['admin_item_edit']);
    }

    public function testLastRoleOfUserIsUsed(): void
    {
        $this->login(new InMemoryUser('budi', null, ['ROLE_A', 'ROLE_B']));

        $routes = $this->createFactory([
            'admin_item_index' => ['ROLE_A'],
            'admin_item_edit' => ['ROLE_B'],
        ])->buildInRoles();

        $this->assertFalse($routes['admin_item_index']);
        $this->assertTrue($routes['admin_item_edit']);
    }

    public function testWhitelistAppliesWithoutExplicitBasePath(): void
    {
        $factory = $this->createFactory([], ['admin_item_index'], null);

        $this->assertSame('/', $factory->getBasePath());
        $this->assertSame(['homepage', 'admin_item_edit', 'admin_item_print'], $factory->build()->getKeys());
    }

    /**
     * A route saved without roles in url.yaml ("route: ~") is parsed as null.
     */
    public function testRouteWithoutRolesIsDenied(): void
    {
        $this->login(new InMemoryUser('budi', null, ['ROLE_A']));

        $routes = $this->createFactory(['admin_item_edit' => null])->buildInRoles();

        $this->assertFalse($routes['admin_item_edit']);
    }
}
