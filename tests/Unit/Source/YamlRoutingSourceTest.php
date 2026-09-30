<?php

namespace Kematjaya\URLBundle\Tests\Unit\Source;

use Kematjaya\URLBundle\Source\YamlRoutingSource;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class YamlRoutingSourceTest extends TestCase
{
    /**
     * @var string
     */
    private $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/kmj-url-source-' . uniqid();
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->dir);
    }

    private function createSource(): YamlRoutingSource
    {
        return new YamlRoutingSource(new ParameterBag(['url' => [
            'resources_dir' => $this->dir,
            'resources_file' => 'url.yaml',
        ]]));
    }

    /**
     * getAll() runs on every request; reading must not write the file (it
     * fails on read-only deployments).
     */
    public function testGetAllWithoutFileReturnsEmptyArrayWithoutWriting(): void
    {
        $source = $this->createSource();

        $this->assertSame([], $source->getAll());
        $this->assertFileDoesNotExist($source->getPath());
    }

    public function testDumpMergesWithExistingRoutesAndDropsRoleUser(): void
    {
        (new Filesystem())->dumpFile($this->dir . '/url.yaml', Yaml::dump(['old_route' => ['ROLE_A']]));
        $source = $this->createSource();

        $count = $source->dump(['new_route' => ['ROLE_USER', 'ROLE_B']]);

        // the returned count covers every route written to the file
        $this->assertSame(2, $count);
        $this->assertSame([
            'old_route' => ['ROLE_A'],
            'new_route' => ['ROLE_B'],
        ], $source->getAll());
    }

    public function testDumpCreatesFile(): void
    {
        $source = $this->createSource();

        $source->dump(['item_index' => ['ROLE_A']]);

        $this->assertSame(['item_index' => ['ROLE_A']], $source->getAll());
    }

    public function testFileWithoutMappingIsTreatedAsEmpty(): void
    {
        $source = $this->createSource();
        (new Filesystem())->dumpFile($source->getPath(), 'bukan mapping');

        $this->assertSame([], $source->getAll());
        $this->assertSame(1, $source->dump(['item_index' => ['ROLE_A']]));
        $this->assertSame(['item_index' => ['ROLE_A']], $source->getAll());
    }
}
