<?php

namespace AppBundle\Form;

use AppBundle\Entity\Client;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ClientType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, array(
                'label' => 'client.firstname'
            ))
            ->add('lastname', TextType::class, array(
                'label' => 'client.lastname'
            ))
            ->add('contacts', CollectionType::class, array(
                'label' => false,
                'entry_type' => ContactType::class,
                'allow_add' => true,
                'allow_delete'  => true,
                'by_reference'  => false
            ))
            ->add('birthdate', DateTimeType::class, array(
                'attr' => array(
                    'class'=>'birthdate_datepicker'
                ),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'label' => 'client.birthdate'
            ))
            ->add('gender', ChoiceType::class, array(
                'choices'  => array(
                    'sex.homme' => "Homme",
                    'sex.femme' => "Femme",
                ),
            ))
            ->add('coordinates', CollectionType::class, array(
                'label' => false,
                'entry_type' => CoordinateType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference'  => false
            ))
            ->add('picture', ImageType::class, array(
                'label' => 'client.picture',
                'required'  => false,
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Client::class,
        ));
    }
}
