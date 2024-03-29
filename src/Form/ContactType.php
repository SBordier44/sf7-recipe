<?php

namespace App\Form;

use App\Dto\ContactDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => t('contactForm.name'),
                'empty_data' => '',
            ])
            ->add('email', EmailType::class, [
                'label' => t('contactForm.email'),
                'empty_data' => '',
            ])
            ->add('message', TextareaType::class, [
                'label' => t('contactForm.message'),
                'empty_data' => '',
            ])
            ->add('service', ChoiceType::class, [
                'label' => t('contactForm.service'),
                'choices' => [
                    t('contactForm.serviceList.customer')->getMessage() => 'service_client',
                    t('contactForm.serviceList.sales')->getMessage() => 'service_commercial',
                    t('contactForm.serviceList.technical')->getMessage() => 'service_technique',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => t('contactForm.send'),
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactDto::class,
        ]);
    }
}
