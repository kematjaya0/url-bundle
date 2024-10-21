<?php

namespace Kematjaya\URLBundle\Source;

/**
 * @package Kematjaya\URLBundle\Source
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface RoutingSourceInterface 
{
    
    /**
     * get storage path
     * 
     * @return string
     */
    public function getPath():string;
    
    /**
     * get all route path available
     * @return array
     */
    public function getAll():array;
    
    /**
     * dump array route path to storage
     * @param array $routers
     * @return int row updated
     */
    public function dump(array $routers):int;
}
