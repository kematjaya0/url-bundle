<?php

namespace Kematjaya\URLBundle\Type;

use Kematjaya\URLBundle\Transformer\AccessControlTransformer;
use Kematjaya\URLBundle\Repository\URLRepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AccessControlType extends AbstractType
{
    private URLRepositoryInterface $URLRepository;
    private AccessControlTransformer $accessControlTransformer;

    public function __construct(AccessControlTransformer $accessControlTransformer, URLRepositoryInterface $URLRepository)
    {
        $this->URLRepository = $URLRepository;
        $this->accessControlTransformer = $accessControlTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $routers = $this->URLRepository->findAll($options['role']);
        $builder->add('role', HiddenType::class, [
            'data' => $options['role']
        ]);
        foreach ($routers as $name => $data) {
            if (empty($data)) {

                continue;
            }

            $builder
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
