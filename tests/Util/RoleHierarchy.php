<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Tests\Util;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

/**
 * @package Kematjaya\URLBundle\Tests\Util
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class RoleHierarchy implements RoleHierarchyInterface
{
    
    public function getReachableRoleNames(array $roles): array
    {
        return [];
    }

}
