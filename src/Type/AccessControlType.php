<?php

namespace Kematjaya\URLBundle\Type;

use Kematjaya\URLBundle\Repository\URLRepositoryInterface;
use Kematjaya\URLBundle\Transformer\AccessControlTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package Kematjaya\URLBundle\Type
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class AccessControlType extends AbstractType
{
    public function __construct(
        private readonly AccessControlTransformer $accessControlTransformer,
        private readonly URLRepositoryInterface $URLRepository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $routers = $this->URLRepository->findAll($options['role']);
        $builder->add('role', HiddenType::class, [
            'data' => $options['role'],
        ]);
        foreach ($routers as $name => $data) {
            if (empty($data)) {

                continue;
            }

            $builder
                ->add($name, CollectionType::class, [
                    'entry_type' => ControlType::class,
                    'data' => [$name => $data],
                    'label' => false,
                ]);
        }

        $builder->addModelTransformer($this->accessControlTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('role');
    }
}
