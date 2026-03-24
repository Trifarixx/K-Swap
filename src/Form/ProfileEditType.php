<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudonyme', TextType::class, [
                'label' => 'Ton Pseudo',
                'attr' => ['class' => 'form-input', 'placeholder' => 'Ex: Twice4Life'],
                'constraints' => [
                    new NotBlank(['message' => 'Merci de saisir un pseudo']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Ton pseudo doit faire au moins {{ limit }} caractères',
                        'max' => 30,
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Ton Email',
                'attr' => ['class' => 'form-input', 'placeholder' => 'Ex: fan@kpop.com'],
                'constraints' => [
                    new NotBlank(['message' => 'Merci de saisir un email']),
                ],
            ])
            ->add('avatarFile', FileType::class, [
                'label' => 'Ton Avatar (Image)',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-input-file'],
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => 'Merci d\'uploader une image valide (JPEG, PNG, WEBP)',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
