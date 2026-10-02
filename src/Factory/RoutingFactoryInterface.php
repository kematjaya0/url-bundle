<?php

namespace Kematjaya\URLBundle\Factory;

use Doctrine\Common\Collections\Collection;

/**
 * @package Kematjaya\URLBundle\Factory
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface RoutingFactoryInterface
{
    public function getAll(): array;
    public function build(): Collection;

    public function setBasePath(string $basePath): self;

    public function getBasePath(): ?string;

    public function buildInRoles(): Collection;

}
