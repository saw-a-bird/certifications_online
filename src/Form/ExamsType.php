<?php

namespace App\Form;

use App\Entity\Exams;
use App\Entity\Certifications;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\{FileType, TextType, TextareaType};
use Symfony\Component\Validator\Constraints\File;

class ExamsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('certification', EntityType::class,
            array(
                'class' => Certifications::class,
                'choice_label' => 'title',
                'disabled' => "disabled"
            ))
            ->add('code', TextType::class)
            ->add('title', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exams::class,
        ]);
    }
}
