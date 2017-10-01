<?php

namespace AppBundle\Form;

use AppBundle\Entity\EventCancellation;
use Doctrine\Bundle\DoctrineBundle\Tests\DependencyInjection\TestType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventCancellationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $label_format = 'event.form.cancellationReason';
        if ($options['isAdmin']) {
            $label_format = 'admin.event.form.cancellationReason';
        }
        $builder
            ->add('reason', TextType::class, array(
                'label_format' => $label_format,
                'attr' => [
                    'autofocus' => true
                ]
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => EventCancellation::class,
            'isAdmin' => false,
        ));
    }

}
