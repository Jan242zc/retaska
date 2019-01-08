<?php

namespace App\Form;

use App\Entity\Objednavka;
use App\Entity\Country;
use App\Entity\Payment;
use App\Entity\Delivery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ObjednavkaEshopType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('phone')
            ->add('customer')
            ->add('street')
            ->add('city')
            ->add('psc')
            ->add('note')           
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name'            
            ])
            ->add('delivery', EntityType::class, [
                'class' => Delivery::class,
                'choice_label' => 'name',
                'choice_attr' => function($delivery) {
                            return ['data-price' => $delivery->getPrice()];
                        }
            ])
            ->add('payment', EntityType::class, [
                'class' => Payment::class,
                'choice_label' => 'name',
                'choice_attr' => function($payment) {
                            return ['data-price' => $payment->getPrice()];
                        }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Objednavka::class,
        ]);
    }
}