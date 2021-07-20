<?php

/**
 * This file is part of the url-bundle.
 */

namespace Kematjaya\URLBundle\Type;

use Kematjaya\URLBundle\Type\ControlType;
use Kematjaya\URLBundle\Transformer\AccessControlTransformer;
use Kematjaya\URLBundle\Repository\URLRepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @package Kematjaya\URLBundle\Type
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class AccessControlType extends AbstractType
{
    /**
     * 
     * @var URLRepositoryInterface
     */
    private $URLRepository;
    
    /**
     * 
     * @var AccessControlTransformer
     */
    private $accessControlTransformer;
    
    public function __construct(AccessControlTransformer $accessControlTransformer, URLRepositoryInterface $URLRepository) 
    {
        $this->URLRepository = $URLRepository;
        $this->accessControlTransformer = $accessControlTransformer;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $routers = $this->URLRepository->findAll($options['role']);
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
          
        $builder->addModelTransformer($this->accessControlTransformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('role');
    }
}
