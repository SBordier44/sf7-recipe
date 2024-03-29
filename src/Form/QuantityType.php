<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Quantity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuantityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', NumberType::class, [
                'html5' => true,
                'attr' => [
                    'step' => 0.1,
                ],
                'scale' => 2,
                'label' => 'Quantité',
            ])
            ->add('unit', ChoiceType::class, [
                'choices' => [
                    'g' => 'g',
                    'kg' => 'kg',
                    'ml' => 'ml',
                    'cl' => 'cl',
                    'l' => 'l',
                    'cuillère à café' => 'cuillère à café',
                    'cuillère à soupe' => 'cuillère à soupe',
                    'pincée' => 'pincée',
                    'unité' => 'unité',
                ],
                'label' => 'Unité'
            ])
            ->add('ingredient', EntityType::class, [
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'label' => 'Ingrédient',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Quantity::class,
        ]);
    }
}
