<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Tests\Util;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

/**
 * @package Kematjaya\URLBundle\Tests\Util
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class UrlGenerator implements UrlGeneratorInterface 
{
    private $context;
    
    public function generate(string $name, array $parameters = array(), int $referenceType = self::ABSOLUTE_PATH): string 
    {
        return $name;
    }

    public function getContext(): RequestContext 
    {
        return $this->context;
    }

    public function setContext(RequestContext $context) 
    {
        $this->context = $context;
    }

}
