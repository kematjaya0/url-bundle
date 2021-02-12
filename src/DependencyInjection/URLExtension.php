<?php

namespace Kematjaya\URLBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLExtension extends Extension 
{
    
    public function load(array $configs, ContainerBuilder $container) 
    {
        $locator = new FileLocator(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Resources/config');
        $loader = new YamlFileLoader($container, $locator);
        $loader->load('services.yaml');
    }

}
