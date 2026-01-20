<?php

namespace Kematjaya\URLBundle\Tests;

use Kematjaya\URLBundle\URLBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class AppKernelTest extends Kernel
{
    public function registerBundles():iterable
    {
        return [
            new URLBundle(),
            new FrameworkBundle(),
            new TwigBundle(),
            new SecurityBundle()
        ];
    }
    
    public function registerContainerConfiguration(LoaderInterface $loader):void
    {
        $loader->load(function (ContainerBuilder $container) use ($loader) 
        {
            $loader->load(__DIR__ . DIRECTORY_SEPARATOR . 'config/config.yml');
            $loader->load(__DIR__ . DIRECTORY_SEPARATOR . 'config/services.yaml');
            
            $container->addObjectResource($this);
        });
    }
}
