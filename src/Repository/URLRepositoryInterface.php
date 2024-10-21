<?php

namespace Kematjaya\URLBundle\Repository;

/**
 * @package Kematjaya\URLBundle\Repository
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface URLRepositoryInterface 
{
    public function findAll(string $role):array;
    
    public function save(array $routers):void;
}
