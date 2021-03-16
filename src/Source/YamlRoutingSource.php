<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Source;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @package Kematjaya\URLBundle\Source
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class YamlRoutingSource implements RoutingSourceInterface
{
    
    /**
     * 
     * @var string
     */
    private $basePath;
    
    public function __construct(ParameterBagInterface $bag) 
    {
        $this->basePath = $bag->get('kernel.project_dir');
    }
    
    public function getAll(): array 
    {
        $filesystem = new Filesystem();
        $filePath = $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'url.yaml';
        if (!$filesystem->exists($filePath)) {
            
            $filesystem->dumpFile($filePath, '');
        }
        
        $menus = Yaml::parseFile($filePath);
        
        return null !== $menus ? $menus : [];
    }

}
