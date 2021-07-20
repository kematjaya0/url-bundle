<?php

/**
 * This file is part of the cash-in.
 */

namespace Kematjaya\URLBundle\Transformer;

use Symfony\Component\Form\DataTransformerInterface;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;

/**
 * @package Kematjaya\URLBundle\Transformer
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class AccessControlTransformer implements DataTransformerInterface
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
    
    public function reverseTransform($value) 
    {
        $role = $value['role'];
        $routers = $this->routingSource->getAll();
        foreach ($value as $x => $val) {
            if ('role' === $x) {
                
                continue;
            }
            
            foreach ($val as $creds) {
                foreach ($creds as $route => $credential) {
                    if ($credential) {
                        $routers[$route] = in_array($role, $routers[$route]) ? $routers[$route] : array_merge([$role], $routers[$route]);
                        continue;
                    }
                    
                    if (!in_array($role, $routers[$route])) {
                        
                        continue;
                    }
                    
                    $routers[$route] = array_filter($routers[$route], function ($row) use ($role) {
                        return $row !== $role;
                    });
                }   
            }
            
        }
        
        return $routers;
    }

    public function transform($value) 
    {
        
        return $value;
    }

}
