<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nb_avis', IntegerType::class, [
                'label' => 'Note',
                'attr' => ['min' => 1, 'max' => 5]
            ])
            ->add('commentaires', TextareaType::class, ['label' => 'Descriptif d\'avis'])
            ->add('status', ChoiceType::class, [
                'label' => 'État de modération de l\'avis',
                'choices' => [
                    'Validé' => 'validé',
                    'Suspendu' => 'suspendu'
                ],
                'data' => 'validé', // valeur par défaut
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}