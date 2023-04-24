<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
// Appel de mon entity pour pouvoir utiliser ces method
use App\Entity\Tricks;
// Transmission de mon formulaire cedit
use App\Form\CreateFormType;
// Soumission du formulaire et persistance des données dans la BDD
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class CreateController extends AbstractController
{

    private $twig;
    private $entityManager;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager) {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/create", name="app_create")
     */

     public function index(Request $request, SluggerInterface $slugger): Response
    {

        // Mon formulaire de modification de trick
        $createForm = new Tricks();

        $form = $this->createForm(CreateFormType::class, $createForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Attribué la date de la creation à mon champ date_create
            $date= new \DateTime;
            $dateCreate = $form->get('date_create')->getData();
            $dateCreate = $date;
            $createForm->setDateCreate($dateCreate);

            // Récupérer l'id du user connecté et l'attribuer au champs user du form
            $userConnected = $this->getUser();
            $idUser = $form->get('user')->getData();
            $idUser = $userConnected;
            $createForm->setUser($idUser);


            
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
                $createForm->setImage($newFilename);
            }


            $this->entityManager->persist($createForm);
            $this->entityManager->flush();

            // Changer la route plus tard pour /detail/{id}
            return $this->redirectToRoute('app_home');
        }


        return $this->render('create/index.html.twig', [
            'controller_name' => 'CreateController',
            'create_form' => $form->createView()
        ]);
    }
}