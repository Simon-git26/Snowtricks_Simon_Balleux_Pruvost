<?php

namespace App\Form;

use App\Entity\Trick;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\All;

// Personalisation de mon formulaire
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /* Titre */ 
            ->add('title', TextType::class, [
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

            
            

            // Ajouter un dl d'image
            ->add('medias', FileType::class, [
                'label' => 'image',
                'multiple' => true,
                'mapped' => false,
                'required' => false,

                'constraints' => [
                    new All([
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
