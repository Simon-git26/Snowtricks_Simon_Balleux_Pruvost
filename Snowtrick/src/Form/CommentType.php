<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Personalisation de mon formulaire
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', null, [
                'label' => 'Commentaire',
            ])
            
            // Type hidden trick
            ->add('trick', HiddenType::class)
            // Type hidden user
            ->add('user', HiddenType::class)
            // Type hidden date
            ->add('date_create', HiddenType::class)
            // Type hidden isactif
            ->add('is_actif', HiddenType::class)

            // Bouton submit
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
