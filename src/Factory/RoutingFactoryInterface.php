<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Factory;

use Doctrine\Common\Collections\Collection;

/**
 * @package Kematjaya\URLBundle\Factory
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface RoutingFactoryInterface 
{
    
    /**
     * 
     * @return array
     */
    public function getAll():array;
    /**
     * 
     * @return Collection
     */
    public function build(): Collection;
    
    /**
     * 
     * @param string $basePath
     * @return RoutingFactoryInterface
     */
    public function setBasePath(string $basePath):self;
    
    /**
     * 
     * @return string|null
     */
    public function getBasePath():?string;
    
}
