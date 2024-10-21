<?php

namespace Kematjaya\URLBundle\Storage;

use Kematjaya\URLBundle\Factory\RoutingFactoryInterface;
use Kematjaya\URLBundle\Storage\CredentialStorageInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class RoutingCredentialStorage implements CredentialStorageInterface
{
    private Collection $routings;

    private RoutingFactoryInterface $routingFactory;
    
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
