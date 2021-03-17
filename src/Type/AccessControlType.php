<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Type;

use Kematjaya\URLBundle\Type\ControlType;
use Kematjaya\URLBundle\Source\RoutingSourceInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @package Kematjaya\URLBundle\Type
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class AccessControlType extends AbstractType implements DataTransformerInterface
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
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $routers = $this->getGroupedRouters($options['role']);
        foreach ($routers as $name => $data) {
            if (empty($data)) {
                
                continue;
            }
            
            $builder
                ->add('role', HiddenType::class, [
                    'data' => $options['role']
                ])
                ->add($name, CollectionType::class, [
                    'entry_type' => ControlType::class,
                    'data' => [$name => $data],
                    'label' => false
                ]);
        }
          
        $builder->addModelTransformer($this);
    }
    
    protected function getGroupedRouters(string $role):array
    {
        $routers = $this->routingSource->getAll();
        $groups = array_filter($routers, function ($row) {
            
            return preg_match("/index\z/i", $row);
        }, ARRAY_FILTER_USE_KEY);
        
        $values = [];
        foreach ($groups as $k => $group) {
            $name = str_replace("_index", '', $k);
            $values[$name] = array_filter($routers, function ($key) use ($k, $name) {
                
                return ($key !== $k and preg_match("/^" . $name . "/i", $key));
            }, ARRAY_FILTER_USE_KEY);
            
            foreach ($values[$name] as $n => $roles) {
                $values[$name][$n] = in_array($role, $roles);
            }
        }
        
        return $values;
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('role');
    }
}
