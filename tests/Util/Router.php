<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Tests\Util;

use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * @package Kematjaya\URLBundle\Tests\Util
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Router implements RouterInterface
{
    
    /**
     * 
     * @var RequestContext
     */
    private $context;
    
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string 
    {
        
    }

    public function getContext(): RequestContext 
    {
        return $this->context;
    }

    public function getRouteCollection(): \Symfony\Component\Routing\RouteCollection 
    {
        return new \Symfony\Component\Routing\RouteCollection();
    }

    public function match(string $pathinfo): array 
    {
        return [];
    }

    public function setContext(RequestContext $context) 
    {
        $this->context = $context;
    }

}
