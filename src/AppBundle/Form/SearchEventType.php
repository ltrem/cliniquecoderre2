<?php

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SearchEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('search_client', EntityType::class, array(
                'class'  => 'AppBundle:Client',
                'choice_label'  => 'fullname',
                'required'  => false,
                'attr' => ['data-select' => 'true']
            ))
            ->add('search_phone', TextType::class, array(
                'required'  => false,
            ))
            ->add('search_emergency', CheckboxType::class, array(
                'label' => 'admin.event.filter.emergency',
                'required'  => false,
            ));
    }

}
