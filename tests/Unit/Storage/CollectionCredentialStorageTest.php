<?php

namespace Kematjaya\URLBundle\Tests\Unit\Storage;

use Kematjaya\URLBundle\Storage\CollectionCredentialStorage;
use PHPUnit\Framework\TestCase;

class CollectionCredentialStorageTest extends TestCase
{
    public function testUnknownRouteIsAllowed(): void
    {
        $this->assertTrue((new CollectionCredentialStorage())->getAccess('any_route'));
    }

    public function testStoredAccessIsReturned(): void
    {
        $storage = (new CollectionCredentialStorage())->setAccess('item_edit', false);

        $this->assertFalse($storage->getAccess('item_edit'));
        $this->assertCount(1, $storage->getAccesses());
    }
}
