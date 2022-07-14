<?php

/**
 * This file is part of the kematjaya/url-bundle.
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
    
    public function getPath():string
    {
        return $this->filePath;
    }
    
    public function getAll(): array 
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->getPath())) {
            $this->dump([]);
        }
        
        $menus = Yaml::parseFile($this->getPath());
        
        return null !== $menus ? $menus : [];
    }
    
    /**
     * 
     * @param array $routers
     * @return void
     * @throws Exception
     */
    public function dump(array $routers):int
    {
        
        $existing = $this->getAll();
        try {
            foreach (array_keys($existing) as $key) {
                if (isset($routers[$key])) {
                    unset($routers[$key]);
                }
            }
            
            $paths = array_merge($existing, $routers);
            
            $string = Yaml::dump($paths);
            $filesystem = new Filesystem();
            $filesystem->dumpFile($this->getPath(), $string);
            
            return count($routers);
        } catch (Exception $ex) {
            throw $ex;
        }
        
        return 0;
    }

}
