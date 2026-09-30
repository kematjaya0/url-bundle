<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Storage;

use Kematjaya\URLBundle\Factory\RoutingFactoryInterface;
use Kematjaya\URLBundle\Storage\CredentialStorageInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @package Kematjaya\URLBundle\Storage
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RoutingCredentialStorage implements CredentialStorageInterface, ResetInterface
{
    private Collection $routings;

    public function __construct(private RoutingFactoryInterface $routingFactory, string $basePath = '/')
    {
        $routingFactory->setBasePath($basePath);

        $this->routings = new ArrayCollection();
    }

    public function getAccess(string $routeName): bool
    {
        if (!$this->getAccesses()->offsetExists($routeName)) {

            return true;
        }

        return $this->getAccesses()->offsetGet($routeName);
    }

    public function getAccesses(): Collection
    {
        if ($this->routings->isEmpty()) {
            $this->routings = $this->routingFactory->buildInRoles();
        }

        return $this->routings;
    }

    public function setAccess(string $routeName, bool $access): CredentialStorageInterface
    {
        $this->getAccesses()->offsetSet($routeName, $access);

        return $this;
    }

    /**
     * Hak akses dihitung untuk user yang sedang login, jadi tidak boleh terbawa
     * ke request berikutnya bila container dipakai ulang (worker, functional test).
     */
    public function reset(): void
    {
        $this->routings = new ArrayCollection();
    }
}
