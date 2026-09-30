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

    public function testRouteNameIsNotTreatedAsRegex(): void
    {
        $repository = new URLRepository(new ArrayRoutingSource([
            'app.item_index' => ['ROLE_A'],
            'app.item_edit' => ['ROLE_A'],
            // cocok dengan /^app.item/ bila titik tidak di-escape
            'appXitem_print' => ['ROLE_A'],
            'report(v2)_index' => ['ROLE_A'],
        ]));

        $this->assertSame([
            'app.item' => ['app.item_index' => true, 'app.item_edit' => true],
            'report(v2)' => ['report(v2)_index' => true],
        ], $repository->findAll('ROLE_A'));
    }

    public function testScalarRoleInSourceIsAccepted(): void
    {
        $repository = new URLRepository(new ArrayRoutingSource(['item_index' => 'ROLE_A']));

        $this->assertSame(['item' => ['item_index' => true]], $repository->findAll('ROLE_A'));
    }

    public function testSaveDumpsRoutes(): void
    {
        $source = new ArrayRoutingSource();

        (new URLRepository($source))->save(['item_index' => ['ROLE_A']]);

        $this->assertSame(['item_index' => ['ROLE_A']], $source->getAll());
    }
}
