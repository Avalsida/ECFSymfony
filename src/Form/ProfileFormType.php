<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le nom ne peut pas être vide.']),
                ],
                // 'attr' => ['class' => 'form-control'],
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom ne peut pas être vide.']),
                ],
                // 'attr' => ['class' => 'form-control'],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse email ne peut pas être vide.']),
                    new Email(['message' => 'Veuillez entrer une adresse email valide.']),
                ],
                // 'attr' => ['class' => 'form-control'],
            ])
            ->add('current_password', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [],
                // 'attr' => ['class' => 'form-control'],
            ])
            ->add('plainPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères.',
                        'max' => 4096,
                    ]),
                ],
                // 'attr' => ['class' => 'form-control'],
            ])
            ->add('confirmPassword', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [],
                // 'attr' => ['class' => 'form-control'],
            ])
            ->add('date_naissance', DateType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La date de naissance ne peut pas être vide.']),
                ],
                'widget' => 'single_text',
                // 'attr' => ['class' => 'form-control'],
            ])
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'L\'adresse ne peut pas être vide.']),
                ],
                // 'attr' => ['class' => 'form-control'],
            ])
            ->add('code_postal', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le code postal ne peut pas être vide.']),
                ],
                // 'attr' => ['class' => 'form-control'],
            ])
            ->add('ville', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La ville ne peut pas être vide.']),
                ],
                // 'attr' => ['class' => 'form-control'],
            ])
            ->add('numero_telephone', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de téléphone ne peut pas être vide.']),
                ],
                // 'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
