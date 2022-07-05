<?php

namespace App\Form;

use App\Entity\eProvider;
use App\Entity\Certification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\{FileType, TextType, TextareaType};
use Symfony\Component\Validator\Constraints\Image;

class CertificationsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('thumbnail_path', FileType::class, [
                "mapped" => false,
                'label' => 'Thumbnail',
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new Image([
                        'maxSize' => '2048k'
                    ])
                ]
            ])

            ->add('title', TextType::class)
            ->add('eProvider', EntityType::class,
                array(
                    'class' => eProvider::class,
                    'choice_label' => 'name',
                    'disabled' => 'disabled',
                    'label' => 'Provider'
                )
            )
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => '10'
                ]]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Certification::class,
        ]);
    }
}
