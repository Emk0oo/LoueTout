<?php

namespace App\Form;

use PHPUnit\TextUI\XmlConfiguration\Logging\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class AddInstanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'instance',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom pour l\'instance',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Le nom de l\'instance doit contenir au moins {{ limit }} caractères',
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('color1', ColorType::class, [
                'label' => 'Couleur principale',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une couleur',
                    ]),
                    new Regex([
                        'pattern' => '/^#[0-9a-f]{6}$/i',
                        'message' => 'La couleur doit être au format hexadécimal',
                    ]),
                ],
            ])
            ->add('color2', ColorType::class, [
                'label' => 'Couleur secondaire',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une couleur',
                    ]),
                    new Regex([
                        'pattern' => '/^#[0-9a-f]{6}$/i',
                        'message' => 'La couleur doit être au format hexadécimal',
                    ]),
                ],
            ])
            ->add('color3', ColorType::class, [
                'label' => 'Couleur tertiaire',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une couleur',
                    ]),
                    new Regex([
                        'pattern' => '/^#[0-9a-f]{6}$/i',
                        'message' => 'La couleur doit être au format hexadécimal',
                    ]),
                ],
            ])
            ->add('color4', ColorType::class, [
                'label' => 'Couleur accent',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une couleur',
                    ]),
                    new Regex([
                        'pattern' => '/^#[0-9a-f]{6}$/i',
                        'message' => 'La couleur doit être au format hexadécimal',
                    ]),
                ],
            ])
            ->add('admin_email', EmailType::class, [
                'label' => 'Email de l\'administrateur',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un email',
                    ]),
                ],
            ])
            ->add('admin_password', PasswordType::class, [
                'label' => 'Mot de passe de l\'administrateur',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
