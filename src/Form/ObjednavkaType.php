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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ObjednavkaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product')
            ->add('quantity')
            ->add('email')
            ->add('phone')
            ->add('customer')
            ->add('street')
            ->add('city')
            ->add('psc')
            ->add('note')
            ->add('productPrice')
            ->add('deliveryPrice')
            ->add('paymentPriceCZK')
            ->add('paymentPriceEUR')
            ->add('totalPrice')            
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name'            
            ])
            ->add('delivery', EntityType::class, [
                'class' => Delivery::class,
                'choice_label' => 'name'            
            ])
            ->add('payment', EntityType::class, [
                'class' => Payment::class,
                'choice_label' => 'name'            
            ])
            ->add('date')
            ->add('state', ChoiceType::class, [
                'choices' => [
                    'Nová' => 'Nová',
                    'Potvrzená' => 'Potvrzená',
                    'Odeslaná' => 'Odeslaná',
                    'Zrušená' => 'Zrušená'
                    ]
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
