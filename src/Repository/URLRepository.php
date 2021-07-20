<?php

/**
 * This file is part of the cash-in.
 */

namespace Kematjaya\URLBundle\Repository;

use Kematjaya\URLBundle\Source\RoutingSourceInterface;

/**
 * @package Kematjaya\MenuBundle\Repository
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class URLRepository implements URLRepositoryInterface
{
    /**
     * 
     * @var RoutingSourceInterface
     */
    private $routingSource;
    
    
    public function __construct(RoutingSourceInterface $routingSource) 
    {
        $this->routingSource = $routingSource;
    }
    
    public function findAll(string $role):array
    {
        $routers = $this->routingSource->getAll();
        $groups = $this->getIndexRoutes($routers);
        $values = $this->groupingRoutes($routers, $groups, $role);
        
        return $values;
    }
    
    public function save(array $routers):void
    {
        $this->routingSource->dump($routers);
    }
    
    /**
     * Get Index Routes
     * @param array $routers
     * @return array
     */
    protected function getIndexRoutes(array $routers):array
    {
        return array_filter($routers, function ($row) {
            
            return preg_match("/index\z/i", $row);
        }, ARRAY_FILTER_USE_KEY);
    }
    
    
    protected function groupingRoutes(array $routers, array $groups, string $role):array
    {
        $values = [];
        foreach ($groups as $k => $group) {
            $name = str_replace("_index", '', $k);
            $values[$name] = array_filter($routers, function ($key) use ($k, $name, $groups) {
                
                if ($key === $k) {
                    return false;
                }
                
                return (preg_match("/^" . $name . "/i", $key));
            }, ARRAY_FILTER_USE_KEY);
            
            foreach ($values[$name] as $n => $roles) {
                $values[$name][$n] = in_array($role, $roles);
            }
        }
        
        return $values;
    }
}
