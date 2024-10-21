<?php

namespace Kematjaya\URLBundle\Storage;

use Doctrine\Common\Collections\Collection;

/**
 * @package App\URLBundle\Storage
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface CredentialStorageInterface 
{
    public function getAccess(string $routeName):bool;
    
    public function setAccess(string $routeName, bool $access):self;
    
    public function getAccesses(): Collection;
}
