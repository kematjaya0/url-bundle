<?php

namespace Kematjaya\URLBundle\Tests\Fixtures;

use Kematjaya\URLBundle\Source\RoutingSourceInterface;

/**
 * In-memory routing source for unit tests.
 */
class ArrayRoutingSource implements RoutingSourceInterface
{
    public function __construct(public array $routes = []) {}

    public function getPath(): string
    {
        return 'memory';
    }

    public function getAll(): array
    {
        return $this->routes;
    }

    public function dump(array $routers): int
    {
        $this->routes = array_merge($this->routes, $routers);

        return count($routers);
    }
}
