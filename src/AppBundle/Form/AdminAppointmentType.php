<?php

namespace AppBundle\Form;

use AppBundle\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminAppointmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client', EntityType::class, array(
                'class' => 'AppBundle\Entity\Client',
                'required' => true,
            ))
            ->add('employe', EntityType::class, array(
                'class' => 'AppBundle\Entity\Employe',
                'required' => true,
                //'expanded' => true,
            ))
            ->add('name', TextType::class, array(
                'label' => 'admin.event.selectReason',
            ))
            ->add('emergency', CheckboxType::class, array(
                'label' => 'admin.event.form.emergency',
                'required' => false,
            ))
            ->add('startTime', DateTimeType::class, array(
                'label_format' => 'admin.event.form.startTime',
                'attr' => array(
                    'id' => 'event_startTime',
                    'class'=>'event_datetimepicker',
                ),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm',
                'required' => true,
                'view_timezone' => 'America/Montreal'
            ))
            ->add('endTime', DateTimeType::class, array(
                'label' => false,
                'attr' => array(
                    'id'=>'event_endTime',
                    'class'=>'form_datetime hidden'
                ),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd HH:mm',
                'required' => false,
                'view_timezone' => 'America/Montreal'
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
