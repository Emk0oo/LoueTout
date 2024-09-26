<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label', TextType::class, [
                'label' => "Name of product",
                'attr' => [
                    'placeholder' => 'Table'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => "Description of product",
                'attr' => [
                    'placeholder' => 'Describe the product...'
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => "Price per day",
                'attr' => [
                    'placeholder' => '100'
                ]
            ])
            ->add('image', FileType::class, [
                'multiple' => true,
                'mapped' => false,
                'attr'     => [
                    'accept' => 'image/*',
                    'multiple' => 'multiple'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
