<?php

namespace App\Form;

use App\Entity\Propositions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\{TextType, CheckboxType};
use Symfony\Component\Validator\Constraints\File;

class PropositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('proposition', TextType::class, [
                'label' => false,
                'required' => false,
                'empty_data' => ''
            ])
            ->add('isCorrect', CheckboxType::class, [
                'label' => 'Correct',
                'required' => false
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Propositions::class,
        ]);
    }
}
