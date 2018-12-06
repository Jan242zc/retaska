<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Order;
use App\Entity\Country;
use App\Entity\Delivery;
use App\Entity\Payment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class OrderType2 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity')
            ->add('email')
            ->add('phone')
            ->add('name')
            ->add('street')
            ->add('city')
            ->add('psc')
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
            ->add('note')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}