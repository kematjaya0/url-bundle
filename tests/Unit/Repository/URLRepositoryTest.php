<?php

namespace Kematjaya\URLBundle\Tests\Unit\Repository;

use Kematjaya\URLBundle\Repository\URLRepository;
use Kematjaya\URLBundle\Tests\Fixtures\ArrayRoutingSource;
use PHPUnit\Framework\TestCase;

class URLRepositoryTest extends TestCase
{
    public function testFindAllGroupsRoutesByIndexRoute(): void
    {
        $repository = new URLRepository(new ArrayRoutingSource([
            'item_index' => ['ROLE_A'],
            'item_edit' => ['ROLE_A'],
            'item_delete' => [],
            'sale_index' => ['ROLE_B'],
        ]));

        $this->assertSame([
            'item' => ['item_index' => true, 'item_edit' => true, 'item_delete' => false],
            'sale' => ['sale_index' => false],
        ], $repository->findAll('ROLE_A'));
    }

    public function testSaveDumpsRoutes(): void
    {
        $source = new ArrayRoutingSource();

        (new URLRepository($source))->save(['item_index' => ['ROLE_A']]);

        $this->assertSame(['item_index' => ['ROLE_A']], $source->getAll());
    }
}
