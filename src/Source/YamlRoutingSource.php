<?php

namespace Kematjaya\URLBundle\Source;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * @package Kematjaya\URLBundle\Source
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class YamlRoutingSource implements RoutingSourceInterface
{
    private readonly string $filePath;

    public function __construct(ParameterBagInterface $bag)
    {
        $configs = $bag->get("url");
        $basePath = $configs["resources_dir"];
        $this->filePath = $basePath . DIRECTORY_SEPARATOR . $configs["resources_file"];
    }

    public function getPath(): string
    {
        return $this->filePath;
    }

    public function getAll(): array
    {
        // runs on every request: never write here, a missing file means no settings
        if (!(new Filesystem())->exists($this->getPath())) {
            return [];
        }

        $menus = Yaml::parseFile($this->getPath());

        // file kosong atau bukan mapping (mis. berisi satu string) dianggap tanpa aturan
        return is_array($menus) ? $menus : [];
    }

    /**
     * @return void
     * @throws Exception
     */
    public function dump(array $routers): int
    {
        $existing = $this->getAll();
        foreach (array_keys($existing) as $key) {
            $existing[$key] = array_values((array) $existing[$key]);
            $routers[$key] ??= $existing[$key];
        }

        $updateRouters = array_map(function (array $roles): array {
            $roles = (array) $roles;
            $key = array_search('ROLE_USER', $roles);
            if (false !== $key) {
                unset($roles[$key]);
            }

            return array_values($roles);
        }, array_merge($existing, $routers));

        $string = Yaml::dump($updateRouters);
        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->getPath(), $string);

        return count($routers);
    }

}
