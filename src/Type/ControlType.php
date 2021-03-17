<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Type;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @package Kematjaya\URLBundle\Type
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ControlType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $name = $builder->getName();
        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($name) {
                $form = $event->getForm();
                foreach ($event->getData() as $k => $value) {
                    
                    $form->add($k, CheckboxType::class, [
                        'data' => $value, 'required' => false,
                        'label' => str_replace($name . '_', '', $k)
                    ]);
                }   
            });
    }
}
