<?php

namespace App\Form;

use App\Entity\Exam;
use App\Entity\Certification;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\{TextType, ChoiceType};
use App\Repository\CertificationsRepository;

class ExamsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $provider = $options["provider"];

        $builder
            ->add('provider', ChoiceType::class, [
                'choices' => [
                    $provider->getName() => true
                ],
                "mapped" => false
            ])
            ->add('certification', EntityType::class,
                array(
                    'class' => Certification::class,
                    'choice_label' => 'title',
                    'query_builder' => function (CertificationsRepository $certificationsRepository) use ($provider)  {
        return  $certificationsRepository->findByProvider($provider->getId());
    },
                )
            )
            ->add('code', TextType::class)
            ->add('title', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exam::class,
            'provider' => null
        ]);
    }
}
