<?php

namespace Kematjaya\URLBundle\Tests\Unit\Storage;

use Doctrine\Common\Collections\ArrayCollection;
use Kematjaya\URLBundle\Factory\RoutingFactoryInterface;
use Kematjaya\URLBundle\Storage\RoutingCredentialStorage;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Service\ResetInterface;

class RoutingCredentialStorageTest extends TestCase
{
    public function testAccessIsBuiltOnceAndRebuiltAfterReset(): void
    {
        $factory = $this->createMock(RoutingFactoryInterface::class);
        $factory->expects($this->once())->method('setBasePath')->with('/admin');
        $factory->expects($this->exactly(2))
            ->method('buildInRoles')
            ->willReturnOnConsecutiveCalls(
                new ArrayCollection(['item_edit' => true]),
                new ArrayCollection(['item_edit' => false])
            );

        $storage = new RoutingCredentialStorage($factory, '/admin');

        $this->assertInstanceOf(ResetInterface::class, $storage);
        $this->assertTrue($storage->getAccess('item_edit'));
        $this->assertTrue($storage->getAccess('item_edit'));
        $this->assertTrue($storage->getAccess('unknown_route'));

        // user berikutnya tidak boleh mewarisi hak akses user sebelumnya
        $storage->reset();

        $this->assertFalse($storage->getAccess('item_edit'));
    }

    public function testSetAccessOverridesBuiltAccess(): void
    {
        $factory = $this->createMock(RoutingFactoryInterface::class);
        $factory->method('buildInRoles')->willReturn(new ArrayCollection(['item_edit' => true]));

        $storage = (new RoutingCredentialStorage($factory))->setAccess('item_edit', false);

        $this->assertFalse($storage->getAccess('item_edit'));
    }
}
