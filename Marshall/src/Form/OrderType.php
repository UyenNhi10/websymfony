<?php

// src/Form/OrderType.php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('mobile')
            ->add('address')
            ->add('dateAt');

        // Check if the "hide_status" option is explicitly set to false
        if (!$options['hide_status']) {
            $builder->add('status');
        }

        $builder
            ->add('user', TextType::class, [
                'disabled' => true, // Lock the "user" field
            ])
            ->add('total', HiddenType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'hide_status' => true, // Set the default value for the custom option
        ]);
    }
}

