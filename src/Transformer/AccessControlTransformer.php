<?php

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
    private RoutingSourceInterface $routingSource;
    
    public function __construct(RoutingSourceInterface $routingSource) 
    {
        $this->routingSource = $routingSource;
    }
    
    /**
     * 
     * @param type $value
     * @return mixed
     */
    public function reverseTransform(mixed $value):mixed
    {
        $role = $value['role'];
        unset($value['role']);
        $routers = $this->routingSource->getAll();
        foreach ($value as $val) {
            foreach ($val as $credentials) {
                $this->process($role, $credentials, $routers);
            }
        }
        
        return $routers;
    }

    /**
     * 
     * @param type $value
     * @return mixed
     */
    public function transform(mixed $value) :mixed
    {
        return $value;
    }

    protected function process(string $role, array $credentials, array &$routers):array
    {
        foreach ($credentials as $route => $credential) {
            if (!isset($routers[$route])) {
                $routers[$route] = [];
            }
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
        
        return $routers;
    }
}
