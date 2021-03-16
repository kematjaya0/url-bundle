<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Factory;

/**
 * @package Kematjaya\URLBundle\Factory
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
abstract class AbstractRoutingFactory implements RoutingFactoryInterface 
{
    
    /**
     * 
     * @var string
     */
    private $basePath;
    
    public function setBasePath(string $basePath): RoutingFactoryInterface
    {
        $this->basePath = $basePath;
        
        return $this;
    }
    
    public function getBasePath():?string
    {
        return $this->basePath;
    }
}
