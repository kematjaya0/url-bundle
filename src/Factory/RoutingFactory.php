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
            
            $pos = strpos($route->getPath(), $this->getBasePath());
            if (false === $pos) {
                
                $routes->offsetSet($name, true);
                continue;
            }
            
            $user = $this->tokenStorage->getToken()->getUser();
            if (!$user instanceof UserInterface) {

                $routes->offsetSet($name, false);
                continue;
            }
            
            
            $routes->offsetSet($name, false);
        }
        
        $userRoles = $user->getRoles();
        $role = end($userRoles);
        foreach ($this->routingSource->getAll() as $name => $routeRoles) {
            if (!$routes->offsetExists($name)) {
                
                continue;
            }
            
            if (!in_array($role, $routeRoles)) {
                
                continue;
            }
            
            $routes->offsetSet($name, true);
        }
        
        return $routes;
    }

}
