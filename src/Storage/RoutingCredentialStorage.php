<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Storage;

use Kematjaya\URLBundle\Factory\RoutingFactoryInterface;
use Kematjaya\URLBundle\Storage\CredentialStorageInterface;
use Doctrine\Common\Collections\Collection;

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
    
    public function __construct(RoutingFactoryInterface $routingFactory, string $basePath = '/') 
    {
        $routingFactory->setBasePath($basePath);
        
        $this->routings = $routingFactory->build();
    }
    
    public function getAccess(string $routeName): bool 
    {
        if (!$this->routings->offsetExists($routeName)) {
            
            return true;
        }
        
        return $this->routings->offsetGet($routeName);
    }

    public function getAccesses(): Collection 
    {
        return $this->routings;
    }

    public function setAccess(string $routeName, bool $access): CredentialStorageInterface 
    {
        $this->access->offsetSet($routeName, $access);
        
        return $this;
    }

}
