<?php

namespace Kematjaya\URLBundle\Tests\Unit\DependencyInjection;

use Kematjaya\URLBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    public function testDefaults(): void
    {
        $config = (new Processor())->processConfiguration(new Configuration(), []);

        $this->assertSame('%kernel.project_dir%/resources', $config['resources_dir']);
        $this->assertSame('url.yaml', $config['resources_file']);
        $this->assertSame([], $config['whitelist']);
    }
}
