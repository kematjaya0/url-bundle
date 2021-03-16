<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Source;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Exception;

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
    private $filePath;
    
    public function __construct(ParameterBagInterface $bag) 
    {
        $basePath = $bag->get('kernel.project_dir');
        $this->filePath = $basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'url.yaml';
    }
    
    public function getAll(): array 
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->filePath)) {
            $this->dump([]);
        }
        
        $menus = Yaml::parseFile($this->filePath);
        
        return null !== $menus ? $menus : [];
    }
    
    /**
     * 
     * @param array $routers
     * @return void
     * @throws Exception
     */
    public function dump(array $routers):void
    {
        try {
            $string = Yaml::dump($routers);
            $filesystem = new Filesystem();
            $filesystem->dumpFile($this->filePath, $string);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}
