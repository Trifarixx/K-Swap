<?php

namespace App\Form;

use App\Entity\Avis;
use App\Entity\Discographie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            // 1. Le lien vers l'album (Rendu Optionnel)
            ->add('discographie', EntityType::class, [
                'class' => Discographie::class,
                'choice_label' => 'titre',
                'label' => 'Lier à un album ?',
                'placeholder' => 'Non, juste un post', // Option vide par défaut
                'required' => false, // <-- C'est ça qui permet le post libre
                'attr' => ['class' => 'form-select'] // Pour le style CSS
            ])

            // 2. La Note (Optionnelle aussi)
            ->add('note', ChoiceType::class, [
                'choices'  => [
                    '⭐' => 1,
                    '⭐⭐' => 2,
                    '⭐⭐⭐' => 3,
                    '⭐⭐⭐⭐' => 4,
                    '⭐⭐⭐⭐⭐' => 5,
                ],
                'label' => 'Une note ?',
                'placeholder' => 'Pas de note',
                'required' => false,
                'attr' => ['class' => 'form-select']
            ])

            // 3. Le Message (Obligatoire)
            ->add('commentaire', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => [
                    'placeholder' => 'Quoi de neuf ?',
                    'rows' => 4,
                    'class' => 'form-textarea'
                ]
            ])

            // 4. L'Image (Champ "virtuel", non lié direct à la BDD)
            ->add('imageFile', FileType::class, [
                'label' => 'Ajouter une photo',
                'mapped' => false, // Important : ce champ n'est pas dans l'entité Avis
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M', // Max 5 Mo
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