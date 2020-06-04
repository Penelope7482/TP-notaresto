<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Restaurant;
use App\Entity\Review;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du Restaurant'
            ])
            ->add('description')
            //  ->add('message', EntityType::class, [
            //      'class' => Review::class, // Quelle classe est reliée au champ message
            //      'choice_label' => 'message'
            //  ])
          // ->add('createdAt', DateTimeType::class, [
          //     'label' => 'Créé le',
          //     'widget' => 'single_text',
          //     'html5' => false,
          //     'format' => 'dd/MM/yyyy HH:mm:ss'
            //])
            ->add('city',  EntityType::class, [
                'class' => City::class, // Quelle classe est reliée au champ message
                'label' => 'Ville',
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
