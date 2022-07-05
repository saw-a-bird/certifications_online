<?php

namespace App\Form;

use App\Entity\Feedback;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\{TextType, ChoiceType, TextareaType};

class FeedbackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $readOnly = $options["readOnly"];

        $builder
            ->add('title', TextType::class, [
                'label' => "Title:", 
                'disabled' => $readOnly
            ]) 
            ->add('description', TextareaType::class, [
                'label' => "Description:",
                'attr' => [
                    'rows' => '10'
                ],
                "disabled" => $readOnly
            ]);

        if ($readOnly == "disabled")
            $builder->add('status', ChoiceType::class, [
                'choices' => [
                    "-" => "-",
                    "Spam" => "Spam",
                    "Verified" => "Verified"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
            "readOnly" => false
        ]);
    }
}
