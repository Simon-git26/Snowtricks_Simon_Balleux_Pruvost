<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


// Appel de mon entity pour pouvoir utiliser ces method
use App\Entity\Trick;
// Transmission de mon formulaire cedit
use App\Form\TrickType;
// Soumission du formulaire et persistance des données dans la BDD
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
//Sotcker mon message en session
use Symfony\Component\HttpFoundation\Session\Session;
// Transmission de mon formulaire comment
use App\Form\CommentType;
use App\Entity\Comment;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;

class TrickController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
    * @Route("/trick/detail/{slug}", name="app_detail")
    */
    public function detail(Trick $trick, Request $request): Response
    {
        // Vérifier si le trick existe
        if (!$trick) {
            throw $this->createNotFoundException('Trick non trouvé !');
        }
      
        /****************** Partie Commentaire ********************/ 
        $comments = $trick->getComments();

        // Mon formulaire de création de commentaires
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        /* Lorsque le formulaire est soumis et valide */
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setDateCreate(new DateTime())->setUser($this->getUser());
            $trick->addComment($comment);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }  
        /****************** Fin Partie Commentaire ********************/ 



        // Envoyer mes données à ma vue
        return $this->render('Trick/detail/index.html.twig', [
            'trick' => $trick,
            'comment_form' => $form->createView(),
            'comments' => $comments
        ]);
    }


    
    /**
     * ******************************************* Fonction pour Editer un Trick **********************
     * @Route("/trick/edit/{slug}", name="app_edit")
     */
    public function edit(Trick $trick,SluggerInterface $slugger, Request $request): Response
    {
       // Pas besoin de check si user existe car route securisé avec IS_AUTHENTICATED_FULLY 

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
            // Image uploader //
            /* Recuperer ma propriete image */
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {

                // Je genere un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $imageFile->guessExtension();

                // Je copie le fichier dans le dossier uploads
                $imageFile->move(
                    $this->getParameter('image_user'),
                    $fichier
                );

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $trick->setImage($fichier);
            }

            $this->entityManager->persist($trick);
            $this->entityManager->flush();
            
            // Changer la route plus tard pour /detail/{id}
            return $this->redirectToRoute('app_home');
        }

        return $this->render('Trick/edit/index.html.twig', [
            'trick' => $trick,
            'edit_form' => $form->createView()
        ]); 
    }


    /**
     ****************************************** Fonction pour Ajouter un Trick **********************
     * @Route("/trick/add", name="app_create")
     */
    public function add(Request $request, SluggerInterface $slugger): Response
    {
        // Pas besoin de check si user existe car route securisé avec IS_AUTHENTICATED_FULLY

        $session = new Session();

        // Mon formulaire de modification de trick
        $createForm = new Trick();

        $form = $this->createForm(TrickType::class, $createForm);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //dd($form->getData());
            //dd($form->getErrors());
            //dd($form->getErrors(true));
            if($form->isValid()) {

                // Attribué la date de la creation
                $date= new \DateTime;
                $createForm->setDateCreate($date);

                // Récupérer l'id du user connecté
                $userConnected = $this->getUser();
                $createForm->setUser($userConnected);
                
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

                // Ajouter le message de succès à la variable de session
                $session->getFlashBag()->add('success', 'Votre trick a été créé avec succès.');


                // Changer la route plus tard pour /detail/{id}
                return $this->redirectToRoute('app_home');
            } else {
                // Traitement en cas d'erreur
                // Ajoutez un message flash pour indiquer l'erreur
                $this->addFlash('error', 'Une erreur s\'est produite lors de la soumission du formulaire.');
    
                return $this->redirectToRoute('app_home'); // Redirection vers la page d'erreur
            }
        }


        return $this->render('Trick/create/index.html.twig', [
            'create_form' => $form->createView()
        ]);
    }


    /**
     * ****************************************** Fonction pour Supprimer un Trick **********************
     * @Route("/trick/delete/{id}", name="app_delete")
     */
    public function delete(Trick $trick): Response
    {
        // Pas besoin de check si user existe car route securisé avec IS_AUTHENTICATED_FULLY

        // Mon formulaire de modification de trick
        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_home');

    }
}
