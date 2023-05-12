<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
// Appel de mon entity pour pouvoir utiliser ces method
use App\Entity\Tricks;
// Soumission du formulaire et persistance des données dans la BDD
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
// Image
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

// Personalisation de mon formulaire
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class EditController extends AbstractController
{
    private $twig;
    private $entityManager;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager) {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/edit/{id}", name="app_edit")
     */
    public function index(ManagerRegistry $doctrine, int $id, Request $request, SluggerInterface $slugger): Response
    {

        // Mon formulaire de modification de trick
        $editForm = $doctrine->getRepository(Tricks::class)->find($id);

        $form = $this->createFormBuilder($editForm)
        ->add('title')
        ->add('description')
        ->add('groupe')

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
                        'image/jpeg',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image',
                ])
            ]
        ])

        ->add('video')
        // Bouton submit
        ->add('submit', SubmitType::class, [
            'label' => 'Sauvegarder !'
        ])
        ->getForm();


        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Attribué une valeur Datetime a mon champs date
            $date= new \DateTime;
            $editForm->setDateCreate($date);

            // Récupérer l'id du user connecté et l'attribuer au champs user
            $userConnected = $this->getUser();
            $editForm->setUser($userConnected);


            // Image uploader //
            /* Recuperer ma propriete image */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                // Créer un nom pour le fichier
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Une fois le servide slugger injecté en debut de fonction, on peut s'en servir
                $safeFilename = $slugger->slug($originalFilename);
                // Recuperer le nouveau filename qu'il vient de créer, avec son id et son extension
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    // Stocke le fichier au niveau du dossier selectionné ici
                    $imageFile->move(
                        $this->getParameter('image_user'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $editForm->setImage($newFilename);
            }



            $this->entityManager->persist($editForm);
            $this->entityManager->flush();
            

            // Changer la route plus tard pour /detail/{id}
            return $this->redirectToRoute('app_home');
        }

        // Recuperer mon trick selon son id
        $trick = $doctrine->getRepository(Tricks::class)->find($id);

        // Erreur si trick n'existe pas
        if (!$trick) {
            echo "Aucun Trick n'a était récupéré";
            die();
        }


        return $this->render('edit/index.html.twig', [
            'controller_name' => 'EditController',
            'trick' => $trick,
            'edit_form' => $form->createView()
        ]);
    }

}
