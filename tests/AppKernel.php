<?php

namespace Kematjaya\URLBundle\Tests;

use Kematjaya\URLBundle\URLBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Minimal kernel for functional tests; configured in PHP so Symfony 5.4 and
 * 6.4 differences can be handled with Kernel::VERSION_ID.
 */
class AppKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new URLBundle(),
        ];
    }

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/kmj-url-bundle/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/kmj-url-bundle/log';
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $framework = [
            'secret' => 'test',
            'test' => true,
            'http_method_override' => false,
            'router' => ['utf8' => true],
            'session' => ['handler_id' => null, 'storage_factory_id' => 'session.storage.factory.mock_file'],
            'csrf_protection' => true,
            'php_errors' => ['log' => true],
        ];
        if (Kernel::VERSION_ID >= 60400) {
            $framework['handle_all_throwables'] = true;
            $framework['session'] += ['cookie_secure' => 'auto', 'cookie_samesite' => 'lax'];
        }
        $container->extension('framework', $framework);

        $security = [
            'role_hierarchy' => [
                'ROLE_ADMINISTRATOR' => ['ROLE_USER'],
                'ROLE_SUPER_USER' => ['ROLE_ADMINISTRATOR'],
            ],
            'providers' => ['memory' => ['memory' => ['users' => []]]],
            'firewalls' => ['main' => ['lazy' => true, 'provider' => 'memory']],
        ];
        if (Kernel::VERSION_ID < 60000) {
            $security['enable_authenticator_manager'] = true;
        }
        $container->extension('security', $security);

        $container->extension('twig', ['strict_variables' => true]);

        $container->extension('url', [
            'resources_dir' => '%kernel.cache_dir%/resources',
            'whitelist' => ['admin_item_print'],
        ]);
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->add('homepage', '/');
        $routes->add('admin_item_index', '/admin/item');
        $routes->add('admin_item_edit', '/admin/item/{id}/edit');
        $routes->add('admin_item_delete', '/admin/item/{id}/delete');
        $routes->add('admin_item_print', '/admin/item/{id}/print');
    }
}
