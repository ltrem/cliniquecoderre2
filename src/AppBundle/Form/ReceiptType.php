<?php

namespace AppBundle\Form;

use AppBundle\Entity\Receipt;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReceiptType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('receiptDate', DateTimeType::class, array(
                'label_format' => 'event.receipt.date',
                'attr' => array(
                    'id' => 'receipt_date',
                    'class' => 'event_datetimepicker'
                ),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd hh:mm',
                'required' => true,
                'view_timezone' => 'America/Montreal',
                'data' => new \DateTime()
            ))
            ->add('amount', MoneyType::class, array(
                'currency' => 'CAD',
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Receipt::class
        ));
    }

}
