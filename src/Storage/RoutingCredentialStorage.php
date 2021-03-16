<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Storage;

use Kematjaya\URLBundle\Factory\RoutingFactoryInterface;
use Kematjaya\URLBundle\Storage\CredentialStorageInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @package Kematjaya\URLBundle\Storage
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RoutingCredentialStorage implements CredentialStorageInterface
{
    
    /**
     * 
     * @var Collection
     */
    private $routings;
    
    /**
     * 
     * @var RoutingFactoryInterface
     */
    private $routingFactory;
    
    public function __construct(RoutingFactoryInterface $routingFactory, string $basePath = '/') 
    {
        $routingFactory->setBasePath($basePath);
        $this->routingFactory = $routingFactory;
        
        $this->routings = new ArrayCollection();
    }
    
    public function getAccess(string $routeName): bool 
    {
        if (!$this->getAccesses()->offsetExists($routeName)) {
            
            return true;
        }
        
        return $this->getAccesses()->offsetGet($routeName);
    }

    public function getAccesses(): Collection 
    {
        if ($this->routings->isEmpty()) {
            $this->routings = $this->routingFactory->buildInRoles();
        }
        
        return $this->routings;
    }

    public function setAccess(string $routeName, bool $access): CredentialStorageInterface 
    {
        $this->getAccesses()->offsetSet($routeName, $access);
        
        return $this;
    }

}
