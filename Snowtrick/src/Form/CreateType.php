<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

// Personalisation de mon formulaire
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CreateType extends AbstractType
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

            
            // Ajouter un dl d'image
            ->add('image', FileType::class, [
                'label' => 'image',
                'mapped' => false,
                'required' => false,
                'data_class' => null,

                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ]
            ])


            /* video*/
            ->add('video', null, [
                'label' => 'Url de la vidéo',
            ])
            

            // Bouton submit
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
