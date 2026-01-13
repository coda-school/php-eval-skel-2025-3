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


class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'property_path' => 'owner.username',
                'label' => 'Username',
                'required' => true,
            ])
            ->add('bio', TextareaType::class, [
                'property_path' => 'owner.bio',
                'label' => 'Bio',
                'required' => false,
                'attr' => ['rows' => 3],
            ])
            ->add('avatarFile', FileType::class, [
                'label' => 'Avatar',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                    ])
                ],
            ])
            ->add('theme', ChoiceType::class, [
                'choices' => [
                    'Light Mode' => 'light',
                    'Dark Mode' => 'dark',
                ],
            ])
            ->add('language', ChoiceType::class, [
                'choices' => [
                    'English' => 'en',
                    'Français' => 'fr',
                    'Español' => 'es',
                ],
            ])
            ->add('notificationsEnabled', CheckboxType::class, [
                'label' => 'Notifications',
                'required' => false,
            ])
            ->add('isPrivateAccount', CheckboxType::class, [
                'label' => 'Private Account',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserSettings::class,
        ]);
    }
}
