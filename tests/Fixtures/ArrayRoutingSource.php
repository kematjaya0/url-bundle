<?php

namespace Kematjaya\URLBundle\Tests\Fixtures;

use Kematjaya\URLBundle\Source\RoutingSourceInterface;

/**
 * In-memory routing source for unit tests.
 */
class ArrayRoutingSource implements RoutingSourceInterface
{
    /**
     * @var array
     */
    public $routes;

    public function __construct(array $routes = [])
    {
        $this->routes = $routes;
    }

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
