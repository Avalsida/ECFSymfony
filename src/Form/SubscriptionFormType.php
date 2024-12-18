<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubscriptionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $monthlyPrice = 23.99;
        $yearlyPrice = $monthlyPrice * 12 * 0.9; // 10% de réduction

        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    sprintf('Mensuel (%.2f €)', $monthlyPrice) => 'monthly',
                   sprintf('Annuel (%.2f € avec 10%% de réduction)', $yearlyPrice) => 'yearly',
                ],
                //Spécifie que les choix seront affichés sous forme de boutons radio (si multiple est false), ou de cases à cocher (si multiple est true).
                'expanded' => true, 
                'multiple' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
