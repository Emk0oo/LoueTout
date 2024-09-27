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
                'label' => 'Instance  name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an instance name',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Instance name must be at least {{ limit }} characters',
                        'max' => 255,
                    ]),
                ],
            ])
            ->add('color1', ColorType::class, [
                'label' => 'Principal color',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a color',
                    ]),
                    new Regex([
                        'pattern' => '/^#[0-9a-f]{6}$/i',
                        'message' => 'Color must be in hexadecimal format',
                    ]),
                ],
            ])
            ->add('color2', ColorType::class, [
                'label' => 'Secondary color',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a color',
                    ]),
                    new Regex([
                        'pattern' => '/^#[0-9a-f]{6}$/i',
                        'message' => 'Color must be in hexadecimal format',
                    ]),
                ],
            ])
            ->add('color3', ColorType::class, [
                'label' => 'Couleur tertiaire',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a color',
                    ]),
                    new Regex([
                        'pattern' => '/^#[0-9a-f]{6}$/i',
                        'message' => 'Color must be in hexadecimal format',
                    ]),
                ],
            ])
            ->add('color4', ColorType::class, [
                'label' => 'Couleur accent',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a color',
                    ]),
                    new Regex([
                        'pattern' => '/^#[0-9a-f]{6}$/i',
                        'message' => 'Color must be in hexadecimal format',
                    ]),
                ],
            ])
            ->add('admin_email', EmailType::class, [
                'label' => 'Administrator email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter an email',
                    ]),
                ],
            ])
            ->add('admin_password', PasswordType::class, [
                'label' => 'Administrator password',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
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
