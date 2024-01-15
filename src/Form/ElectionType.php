<?php

namespace App\Form;

use App\Entity\Candidate;
use App\Entity\Election;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ElectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre de mon election"
            ])
            ->add('candidates', EntityType::class, [
                'label' => 'Candidats',
                'class' => User::class,
                'mapped' => false,
                'choice_label' => 'fullName',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (UserRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', '%"ROLE_USER"%');
                },
            ])
            ->add('untilAt', DateType::class, [
                'label' => "Date limite de l'election",
                'widget' => 'single_text',
                'html5' => true,
                'data' => new \DateTime()
                // Ajoutez d'autres options si nécessaire
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Election::class,
        ]);
    }
}
