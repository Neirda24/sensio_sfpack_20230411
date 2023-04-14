<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use App\Model\Rated;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('plot')
            ->add('poster')
            ->add('releasedAt', DateType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime_immutable',
            ])
            ->add('rated', EnumType::class, [
                'class' => Rated::class,
                'choice_label' => 'value'
            ])
            ->add('genres', EntityType::class, [
                'class'        => Genre::class,
                'choice_label' => 'name',
                'multiple'     => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
