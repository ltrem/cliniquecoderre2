<?php

namespace AppBundle\Form;

use AppBundle\Entity\Schedule;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            /*
            ->add('dateFrom', DateTimeType::class, array(
                'attr' => array(
                    'class'=>'schedule_datepicker',
                ),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => true,
                'view_timezone' => 'America/Montreal'
            ))
            ->add('dateTo', DateTimeType::class, array(
                'attr' => array(
                    'class'=>'schedule_datepicker',
                ),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'required' => true,
                'view_timezone' => 'America/Montreal'
            ))
            ->add('workingDays', ChoiceType::class, array(
                'multiple' => true,
                'expanded' => true,
                'choices'  => array(
                    'Lundi' => '1',
                    'Mardi' => '2',
                    'Mercredi' => '3',
                    'Jeudi' => '4',
                    'Vendredi' => '5',
                    'Samedi' => '6',
                    'Dimanche' => '7'
                ),
            ))
            */
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Schedule::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_schedule';
    }


}
