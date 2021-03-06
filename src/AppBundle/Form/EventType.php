<?php

namespace AppBundle\Form;

use AppBundle\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('employe', EntityType::class, array(
                'class' => 'AppBundle\Entity\Employe',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Soyez bref sur la raison de votre visite'
                )
                //'expanded' => true,
            ))
            ->add('name', TextareaType::class, array(
                'label' => 'event.selectReason',
                'attr' => array(
                    'class' => 'has-feedback',
                    'placeholder' => 'Soyez bref sur la raison de votre visite'
                )
            ))
            ->add('emergency', CheckboxType::class, array(
                'label' => 'event.form.emergency',
                'attr' => array(
                    'class' => 'no-validate'
                ),
                'required' => false,
            ))
            ->add('startTime', DateTimeType::class, array(
                'label_format' => 'event.form.startTime',
                'attr' => array(
                    'id' => 'event_startTime',
                    'class' => 'hidden'
                ),
                'widget' => 'single_text',
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
                'required' => false,
                'view_timezone' => 'America/Montreal'
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Event::class
        ));
    }

}
