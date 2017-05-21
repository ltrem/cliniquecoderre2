<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class AppointmentAvailabilityNotificationAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('answer',  ChoiceType::class, array(
                'choices' => array(
                    'event.availability.answer.yes'   => 1,
                    'event.availability.answer.no'    => 0
                ),
                'expanded'  => true,
            ));
    }
}