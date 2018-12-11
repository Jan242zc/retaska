<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Country;
use App\Entity\Delivery;
use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity')
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
            'choice_label' => 'name'
            ])
            ->add('payment', EntityType::class, [
            'class' => Payment::class,
            'choice_label' => 'name'
            ])
            ->add('product')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
