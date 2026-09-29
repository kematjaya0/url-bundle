<?php

namespace Kematjaya\URLBundle\Tests\Unit\Factory;

use Kematjaya\URLBundle\Factory\RoutingFactory;
use Kematjaya\URLBundle\Tests\Fixtures\ArrayRoutingSource;
use Kematjaya\UserBundle\Entity\DefaultUser;
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

    private function createFactory(array $settings = [], array $whitelist = []): RoutingFactory
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

        return $factory->setBasePath('/admin');
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

    public function testDefaultUserUsesSingleRole(): void
    {
        $user = (new DefaultUser())->setUsername('budi');
        $user->setSingleRole('ROLE_B');
        $this->login($user);

        $routes = $this->createFactory(['admin_item_edit' => ['ROLE_B']])->buildInRoles();

        $this->assertTrue($routes['admin_item_edit']);
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
