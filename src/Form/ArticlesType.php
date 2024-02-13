<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('thumbnail')
            ->add('date')
            ->add('moyenne_avis')
            ->add('id_user', EntityType::class, [
                'class' => User::class,
'choice_label' => 'id',
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
'choice_label' => 'id',
            ])
            ->add('id_categories', EntityType::class, [
                'class' => Categories::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
