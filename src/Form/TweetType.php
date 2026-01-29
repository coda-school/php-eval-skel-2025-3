<?php

namespace App\Form;

use App\Entity\Tweet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TweetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'tweet.placeholder',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'errors.blank']),
                    new Length([
                        'max' => 280,
                        'maxMessage' => 'errors.too_long',
                    ]),
                ],
            ])
            ->add('imageFile', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '10M',
                        'maxSizeMessage' => 'errors.image_size',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'errors.image_format',
                    ])
                ],
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $tweet = $event->getData();
            $form = $event->getForm();

            if ($tweet instanceof Tweet && $tweet->getImageFilename()) {
                $form->add('deleteImage', CheckboxType::class, [
                    'label' => 'edit.delete_image',
                    'mapped' => false,
                    'required' => false,
                    'attr' => ['id' => 'delete-toggle']
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tweet::class,
            'translation_domain' => 'stela',
        ]);
    }
}
