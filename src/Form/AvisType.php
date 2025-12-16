<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ON A SUPPRIMÉ DISCOGRAPHIE ET NOTE ICI

            // 1. Le Message
            ->add('commentaire', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => [
                    'placeholder' => 'Quoi de neuf ?',
                    'rows' => 4,
                    'class' => 'form-textarea' // Assurez-vous d'avoir ce style ou utilisez 'w-full ...'
                ]
            ])

            // 2. L'Image
            ->add('imageFile', FileType::class, [
                'label' => 'Ajouter une photo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci d\'envoyer une image valide (JPG, PNG, WEBP)',
                    ])
                ],
                'attr' => ['class' => 'form-file']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}