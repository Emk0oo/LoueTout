<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ModifyInstanceParametersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('accent', ColorType::class, [
                'label' => 'Accent color',
                'required' => true,
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
            ->add('secondary', ColorType::class, [
                'label' => 'Secondary color',
                'required' => true,
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
