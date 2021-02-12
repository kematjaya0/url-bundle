<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Tests;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @package Kematjaya\URLBundle\Tests
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLBundleTest extends WebTestCase
{
    public static function getKernelClass() 
    {
        return AppKernelTest::class;
    }
    
    public function testInstanceContainer():ContainerInterface
    {
        $client = parent::createClient();
        $container = $client->getContainer();
        $this->assertInstanceOf(ContainerInterface::class, $container);
        
        return $container;
    }
}
