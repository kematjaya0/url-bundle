<?php

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
        $configs = $bag->get("url");
        $basePath = $configs["resources_dir"];
        $this->filePath = $basePath . DIRECTORY_SEPARATOR . $configs["resources_file"];
    }

    public function getPath():string
    {
        return $this->filePath;
    }

    public function getAll(): array
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists($this->getPath())) {
            $string = Yaml::dump([]);
            $filesystem->dumpFile($this->getPath(), $string);
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
                $existing[$key] = array_values($existing[$key]);
                if (isset($routers[$key])) {
                    unset($routers[$key]);
                }
            }

            $updateRouters = array_merge($existing, $routers);

            $string = Yaml::dump($updateRouters);
            $filesystem = new Filesystem();
            $filesystem->dumpFile($this->getPath(), $string);

            return count($routers);
        } catch (Exception $ex) {
            throw $ex;
        }

        return 0;
    }

}
