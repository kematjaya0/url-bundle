<?php

namespace Kematjaya\URLBundle\Tests\Unit\Transformer;

use Kematjaya\URLBundle\Tests\Fixtures\ArrayRoutingSource;
use Kematjaya\URLBundle\Transformer\AccessControlTransformer;
use PHPUnit\Framework\TestCase;

class AccessControlTransformerTest extends TestCase
{
    public function testReverseTransformGrantsAndRevokesRole(): void
    {
        $transformer = new AccessControlTransformer(new ArrayRoutingSource([
            'item_index' => ['ROLE_B'],
            'item_edit' => ['ROLE_A', 'ROLE_B'],
        ]));

        $routes = $transformer->reverseTransform([
            'role' => 'ROLE_A',
            'item' => [['item_index' => true, 'item_edit' => false, 'item_new' => true]],
        ]);

        $this->assertSame(['ROLE_A', 'ROLE_B'], array_values($routes['item_index']));
        $this->assertSame(['ROLE_B'], array_values($routes['item_edit']));
        $this->assertSame(['ROLE_A'], array_values($routes['item_new']));
    }

    public function testRevokedRoleLeavesSequentialList(): void
    {
        $transformer = new AccessControlTransformer(new ArrayRoutingSource([
            'item_edit' => ['ROLE_A', 'ROLE_B', 'ROLE_C'],
            'item_show' => 'ROLE_A',
        ]));

        $routes = $transformer->reverseTransform([
            'role' => 'ROLE_A',
            'item' => [['item_edit' => false, 'item_show' => false]],
        ]);

        $this->assertSame(['ROLE_B', 'ROLE_C'], $routes['item_edit']);
        $this->assertSame([], $routes['item_show']);
    }

    public function testReverseTransformWithoutRoleKeepsRoutes(): void
    {
        $transformer = new AccessControlTransformer(new ArrayRoutingSource(['item_index' => ['ROLE_B']]));

        $this->assertSame(['item_index' => ['ROLE_B']], $transformer->reverseTransform(null));
        $this->assertSame(['item_index' => ['ROLE_B']], $transformer->reverseTransform([]));
    }

    public function testTransformReturnsValue(): void
    {
        $this->assertSame(['x'], (new AccessControlTransformer(new ArrayRoutingSource()))->transform(['x']));
    }
}
