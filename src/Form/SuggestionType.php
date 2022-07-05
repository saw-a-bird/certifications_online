<?php

namespace App\Form;

use App\Entity\eSuggestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{TextType, FileType, NumberType};
use Symfony\Component\Validator\Constraints\File;

class SuggestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eProvider', TextType::class, ['label' => "Exam Provider:*", 'attr' => ['placeholder' => 'Microsoft, Cisco...']]) // for custom
            ->add('examCode', TextType::class, ['label' => "Exam Code:*"]) // by select entity type or by custom text
            ->add('examTitle', TextType::class, ['label' => "Exam Title:*"]) // for custom

            ->add('certificationTitle', TextType::class, ['label' => "Certification Title:", 'required' => false])
            
            ->add('qProvider', TextType::class, ['label' => "Questions Provider:*", 'attr' => ['placeholder' => 'Certkey, Certkiller, Passguide...']])
            ->add('upload_pdf', FileType::class, [
                'data_class' => null,
                'mapped' => false,

                'label' => 'PDF File*',
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => $options["pdf_required"],
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'application/pdf'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF.',
                    ])
                ]
            ])
            ->add('minsUntil', NumberType::class, [
                "label" => "Time (in minutes; min: 30mins, max: 120mins):*",                
                'attr' => array(
                    'min' => '30',
                    'max' => '120',
                )
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => eSuggestion::class,
            'pdf_required' => false
        ]);
    }
}
