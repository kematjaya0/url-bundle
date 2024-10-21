<?php

namespace Kematjaya\URLBundle\Factory;

use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @package Kematjaya\URLBundle\Factory
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RoutingFactory extends AbstractRoutingFactory
{
    private RouterInterface $router;
    private TokenStorageInterface $tokenStorage;
    private RoutingSourceInterface $routingSource;
    private array $configs;

    public function __construct(ParameterBagInterface $bag, RouterInterface $router, TokenStorageInterface $tokenStorage, RoutingSourceInterface $routingSource)
    {
        $this->configs = $bag->get("url");
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->routingSource = $routingSource;
        $this->setBasePath("/");
    }

    public function getRouter(): RouterInterface
    {
        return $this->router;
    }

    public function getAll(): array
    {
        return $this->getRouter()->getRouteCollection()->all();
    }

    public function build(): Collection
    {
        $routes = new ArrayCollection();
        $whitelists = $this->configs["whitelist"];
        foreach ($this->getAll() as $name => $route) {
            if (!$route instanceof Route) {
                continue;
            }

            if (null === $this->getBasePath()) {
                $routes->offsetSet($name, false);
                continue;
            }

            $pos = strpos($route->getPath(), $this->getBasePath());
            if (false === $pos) {

                continue;
            }

            if (in_array($name, $whitelists)) {
                continue;
            }

            $routes->offsetSet($name, false);
        }

        return $routes;
    }

    public function buildInRoles():Collection
    {
        $routes = $this->build();
        $settings = $this->routingSource->getAll();
        $token = $this->tokenStorage->getToken();
        if (null === $token) {

            return $this->updateRole($routes, $settings);
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {

            return $this->updateRole($routes, $settings);
        }

        $roles = $user->getRoles();
        $role = end($roles);

        $this->updateRole($routes, $settings, function ($routes, $routeName) use ($role, $settings) {

            $routes->offsetSet($routeName, in_array($role, $settings[$routeName]));
        });

        return $routes;
    }

    protected function updateRole(Collection &$routes, array $settings, callable $callable = null)
    {
        foreach ($routes as $name => $access) {
            if (!isset($settings[$name])) {
                $routes->offsetSet($name, true);
                continue;
            }

            if (null !== $callable) {

                call_user_func($callable, $routes, $name);
            }
        }


        return $routes;
    }
}
