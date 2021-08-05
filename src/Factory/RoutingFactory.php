<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Factory;

use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Kematjaya\UserBundle\Entity\DefaultUser;

/**
 * @package Kematjaya\URLBundle\Factory
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RoutingFactory extends AbstractRoutingFactory
{
    /**
     * 
     * @var RouterInterface
     */
    private $router;
    
    /**
     * 
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    
    /**
     * 
     * @var RoutingSourceInterface
     */
    private $routingSource;
    
    public function __construct(RouterInterface $router, TokenStorageInterface $tokenStorage, RoutingSourceInterface $routingSource) 
    {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
        $this->routingSource = $routingSource;
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
        if ($user instanceof DefaultUser) {
            $role = $user->getSingleRole();
        }
        
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
