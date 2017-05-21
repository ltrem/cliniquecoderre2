<?php

namespace AppBundle\Form;

use AppBundle\Entity\Coordinate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoordinateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('address')
            ->add('city')
            ->add('province')
            ->add('country')
            ->add('isPrimary', CheckboxType::class, array(
                'required'  => false,
                'attr' => array(
                    'class' => 'isPrimary',
                )
            ));

    }

    public function configureOptions(OptionsResolver $resolver)
    {
            $resolver->setDefaults(array(
                'data_class' => Coordinate::class
            ));
    }
}
