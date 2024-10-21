<?php

namespace Kematjaya\URLBundle\Storage;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @package App\URLBundle\Storage
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class CollectionCredentialStorage implements CredentialStorageInterface
{
    private Collection $access;
    
    public function __construct() 
    {
        $this->access = new ArrayCollection();
    }
    
    public function getAccess(string $routeName): bool 
    {
        if (!$this->access->offsetExists($routeName)) {
            
            return true;
        }
        
        return $this->access->offsetGet($routeName);
    }

    public function getAccesses(): Collection 
    {
        return $this->access;
    }

    public function setAccess(string $routeName, bool $access): CredentialStorageInterface 
    {
        $this->access->offsetSet($routeName, $access);
        
        return $this;
    }

}
