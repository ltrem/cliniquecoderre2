<?php

namespace AppBundle\Form;

use AppBundle\Entity\Contact;
use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneCell', PhoneNumberType::class, array(
                'label' => 'client.phoneCell',
                'default_region' => 'CA',
                'format' => PhoneNumberFormat::NATIONAL,
                'attr' => array(
                    'data-inputmask' => "\"mask\": \"1 (999) 999-9999\"",
                    'data-mask' => '',
                    'class' => 'required'
                ),
                'required'  => true
            ))
            ->add('phoneCellCarrier', null, array(
                'label' => 'client.phoneCellCarrier'
            ))
            ->add('phoneWork', PhoneNumberType::class, array(
                'label' => 'client.phoneWork',
                'default_region' => 'CA',
                'format' => PhoneNumberFormat::NATIONAL,
                'attr' => array(
                    'data-inputmask' => "\"mask\": \"1 (999) 999-9999\"",
                    'data-mask' => '',
                ),
                'required'  => false
            ))
            ->add('phoneHome', PhoneNumberType::class, array(
                'label' => 'client.phoneHome',
                'default_region' => 'CA',
                'format' => PhoneNumberFormat::NATIONAL,
                'attr' => array(
                    'data-inputmask' => "\"mask\": \"1 (999) 999-9999\"",
                    'data-mask' => '',
                ),
                'required'  => false
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
            $resolver->setDefaults(array(
                'data_class' => Contact::class
            ));
    }
}
