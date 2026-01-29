<?php

namespace App\Form;

use App\Entity\UserSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 1. Éponyme
            ->add('username', TextType::class, [
                'property_path' => 'owner.username',
                'label' => 'fields.username',
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'errors.blank']),
                    new Length(['min' => 3, 'max' => 30]),
                ],
                'attr' => ['class' => 'font-bold']
            ])

            // 2. Nom d'affichage
            ->add('displayName', TextType::class, [
                'property_path' => 'owner.displayName',
                'label' => 'fields.display_name',
                'required' => false,
                'constraints' => [new Length(['max' => 30])]
            ])

            // 3. Bio
            ->add('bio', TextareaType::class, [
                'property_path' => 'owner.bio',
                'label' => 'fields.bio',
                'required' => false,
                'attr' => ['rows' => 5],
            ])

            // 4. Avatar
            ->add('avatarFile', FileType::class, [
                'label' => 'fields.avatar',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'errors.image_format',
                    ])
                ],
            ])

            // 5. Thème
            ->add('theme', ChoiceType::class, [
                'label' => 'settings.theme',
                'choices' => [
                    'settings.themes.light' => 'light',
                    'settings.themes.dark' => 'dark',
                ],
            ])

            // 6. Langue
            ->add('language', ChoiceType::class, [
                'label' => 'settings.language',
                'choices' => [
                    'English' => 'en',
                    'Français' => 'fr',
                ],
                'choice_translation_domain' => false,
            ])

            // 7. Notifications
            ->add('notificationsEnabled', CheckboxType::class, [
                'label' => 'settings.notifications',
                'required' => false,
            ])

            // 8. Compte Privé
            ->add('isPrivateAccount', CheckboxType::class, [
                'label' => 'settings.private_account',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserSettings::class,
            'translation_domain' => 'stela',
        ]);
    }
}
