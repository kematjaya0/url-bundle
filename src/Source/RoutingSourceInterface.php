<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Source;

/**
 * @package Kematjaya\URLBundle\Source
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface RoutingSourceInterface 
{
    public function getAll():array;
    
    public function dump(array $routers):void;
}
