<?php

namespace App\Form;

use App\Entity\ExamPaper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{ChoiceType, TextType, NumberType};

class ExamPapersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $exam = $options["exam"];

        $builder
            ->add('exam', ChoiceType::class, [
                'choices' => [
                    $exam->getCode() => true
                ],
                "mapped" => false
            ])
            ->add('qProvider', TextType::class, [
                "label" => "Questions Provider"
            ])
            ->add('minsUntil', NumberType::class, [
                "label" => "Time (in minutes)"
            ])
            ->add('isLocked', ChoiceType::class, [
                "label" => "Locked",
                'choices'  => [
                    'Yes' => true,
                    'No' => false,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExamPaper::class,
            'exam' => null
        ]);
    }
}
