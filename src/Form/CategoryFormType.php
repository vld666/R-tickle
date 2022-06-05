<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotNull([
                        'message' => "Category name can't be null"
                    ])
                ],
                'label' => false,
                'attr' => ['placeholder' => 'Category name']
            ])
            ->add('description', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Description']
            ])
            ->add('color', TextType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Color code']
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
