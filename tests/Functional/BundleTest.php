<?php

namespace Kematjaya\URLBundle\Tests\Functional;

use Kematjaya\URLBundle\Repository\URLRepositoryInterface;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use Kematjaya\URLBundle\Storage\CredentialStorageInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Twig\Environment;

class BundleTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        (new Filesystem())->remove(static::getContainer()->get(RoutingSourceInterface::class)->getPath());
    }

    public function testServicesAreRegistered(): void
    {
        $container = static::getContainer();

        $this->assertInstanceOf(CredentialStorageInterface::class, $container->get(CredentialStorageInterface::class));
        $this->assertInstanceOf(URLRepositoryInterface::class, $container->get(URLRepositoryInterface::class));
        $this->assertIsArray($container->getParameter('url'));
    }

    public function testConfigureCommandDumpsRoutesUnderBasePath(): void
    {
        $tester = new CommandTester((new Application(self::$kernel))->find('url:configure'));
        $tester->setInputs(['/admin']);

        $this->assertSame(0, $tester->execute([]));

        $routes = static::getContainer()->get(RoutingSourceInterface::class)->getAll();
        // admin_item_print is whitelisted; ROLE_USER is never stored
        $this->assertSame(['admin_item_index', 'admin_item_edit', 'admin_item_delete'], array_keys($routes));
        $this->assertSame(['ROLE_SUPER_USER', 'ROLE_ADMINISTRATOR'], $routes['admin_item_edit']);
    }

    public function testTwigFunctionsRenderForAuthorizedRoute(): void
    {
        $container = static::getContainer();
        $container->get(RoutingSourceInterface::class)->dump(['admin_item_edit' => ['ROLE_ADMINISTRATOR']]);
        // CSRF tokens (delete_tag) are stored in the session of the current request
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        $container->get(RequestStack::class)->push($request);
        $user = new InMemoryUser('admin', null, ['ROLE_ADMINISTRATOR']);
        $container->get(TokenStorageInterface::class)->setToken(new UsernamePasswordToken($user, 'main', $user->getRoles()));

        $html = $container->get(Environment::class)
            ->createTemplate("{{ link_to('admin_item_edit', {id: 1}, {label: 'Edit'}, {action: 'ROLE_ADMINISTRATOR', object: user}) }}|{{ delete_tag('delete1', 'admin_item_delete', {id: 1}) }}")
            ->render(['user' => $user]);

        $this->assertStringContainsString('<a href="/admin/item/1/edit" > Edit</a>', $html);
        $this->assertStringContainsString('action="/admin/item/1/delete"', $html);
    }
}
