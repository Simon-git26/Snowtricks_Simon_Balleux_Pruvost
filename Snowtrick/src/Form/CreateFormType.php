<?php

namespace App\Form;

use App\Entity\Tricks;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Personalisation de mon formulaire
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class CreateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /* Titre */ 
            ->add('title', null, [
                'label' => 'Titre',
            ])
            /* Descrption */
            ->add('description', null, [
                'label' => 'Description',
            ])
            /* Groupe*/
            ->add('groupe', null, [
                'label' => 'Groupe',
            ])

            // Type hidden user
            ->add('user', HiddenType::class)
            
            // Type hidden date
            ->add('date_create', HiddenType::class)

            /*
            // Type hidden trick id
            ->add('id', HiddenType::class)
            */

            /*
            ->add('groupe')
            ->add('image')
            ->add('video')
            ->add('user')
            */

            // Bouton submit
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tricks::class,
        ]);
    }
}
