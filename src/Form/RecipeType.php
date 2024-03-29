<?php

namespace App\Form;

use App\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la recette',
            ])
            ->add('quantities', CollectionType::class, [
                'entry_type' => QuantityType::class,
                'by_reference' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'label' => false
                ],
                'attr' => [
                    'class' => 'mb-5 mt-5 mx-5',
                    'data-form-collection-add-label-value' => 'Ajouter un ingrédient',
                    'data-form-collection-delete-label-value' => 'Supprimer cet ingrédient',
                ],
                'label' => 'Quantités',
                'label_attr' => [
                    'class' => 'fw-bold',
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu de la recette',
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée de la recette',
            ])
            ->add('category', CategoryAutocompleteField::class)
            ->add('thumbnailFile', FileType::class, [
                'label' => 'Image de la recette',
                'required' => false,
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
