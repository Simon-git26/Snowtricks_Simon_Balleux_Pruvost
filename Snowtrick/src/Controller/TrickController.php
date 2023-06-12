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
// Pour ma method de delete
use Doctrine\Persistence\ManagerRegistry;
// Transmission de mon formulaire comment
use App\Form\CommentType;
use App\Entity\Comment;




class TrickController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }



    /**
     * @Route("/trick/detail/{id}/{slug}", name="app_detail")
     */
    public function detail(ManagerRegistry $doctrine, int $id, $slug, SluggerInterface $slugger, Request $request): Response
    {
        // Set une variable isConnected pour verifier si un user est connecté
        // Sert à declarer ma logique dans le controller au lieu de le faire dans Twig
        $isConnected = false;
        $userConnected = $this->getUser();

        $roleUserConnected = "";
        
        // Si un user est connecté
        if ($userConnected) {
            $isConnected = true;

            $roleUserConnected = $this->getUser()->getRoles()[0];
        }

        // Meme principe que pour user connecté mais la pour savoir si son role est admin
        $isAdmin = false;

        if ($roleUserConnected == "ROLE_ADMIN") {
            $isAdmin = true;
        }
        
        
        // Mon formulaire de creation de commentaires
        $commentForm = new Comment();

        $form = $this->createForm(CommentType::class, $commentForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupérer l'id du trick et l'attribuer au champs trick du form
            $getIdTrick = $doctrine->getRepository(Trick::class)->find($id);
            $idTrick = $form->get('trick')->getData();
            $idTrick = $getIdTrick;
            $commentForm->setTrick($idTrick);

            // Récupérer l'id du user connecté et l'attribuer au champs user du form
            $userConnected = $this->getUser();
            $idUser = $form->get('user')->getData();
            $idUser = $userConnected;
            $commentForm->setUser($idUser);

            // Attribué la date de la creation à mon champ date_create
            $date= new \DateTime;
            $dateCreate = $form->get('date_create')->getData();
            $dateCreate = $date;
            $commentForm->setDateCreate($dateCreate);

            // Attribué la valeur 0 par de isActif pour mon champ is_actif
            $actif= 0;
            $isActif = $form->get('is_actif')->getData();
            $isActif = $actif;
            $commentForm->setIsActif($isActif);


            $this->entityManager->persist($commentForm);
            $this->entityManager->flush();

            // Changer la route plus tard pour /detail/{id}
            return $this->redirectToRoute('app_home');
        }


        // Recuperer mon trick selon son id
        $trick = $doctrine->getRepository(Trick::class)->find($id);

        // Vérifiez si le slug passé dans l'URL correspond au slug généré à partir du titre de l'article
        $trickSlug = $slugger->slug($trick->getTitle())->lower()->toString();
        
        if ($slug !== $trickSlug) {
            // Redirigez vers l'URL avec le slug correct
            return $this->redirectToRoute('app_detail', [
                'id' => $id,
                'slug' => $trickSlug,
            ]);
        }


        // Récuperer tous les commentaires ou trick_id correspond à l'id du trick en question sur la page detail
        $comments = $doctrine->getRepository(Comment::class)->findBy([
            'trick' => $id
        ]);


        // Erreur si trick n'existe pas
        if (!$trick) {
            echo "Aucun Trick n'a était récupéré";
            die();
        }

        // Envoyer mes donnée a ma view
        // Passé ma var isConnected pour m'en servir dans Twig ainsi que isAdmin pour mon btn corbeille commentaire
        return $this->render('detail/index.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick,
            'comments' => $comments,
            'comment_form' => $form->createView(),
            'isConnected' => $isConnected,
            'isAdmin' => $isAdmin
        ]);
        
    }


    /**
     * ******************************************* Fonction pour Editer un Trick **********************
     * @Route("/trick/edit/{id}/{slug}", name="app_edit")
     */
    public function edit(ManagerRegistry $doctrine, int $id, $slug, SluggerInterface $slugger, Request $request): Response
    {
        // Set une variable isConnected pour verifier si un user est connecté
        // Sert à declarer ma logique dans le controller au lieu de le faire dans Twig
        $isConnected = false;
        $userConnected = $this->getUser();

        // Si un user est connecté
        if ($userConnected) {
            $isConnected = true;
        }

        // Mon formulaire de modification de trick
        $editForm = $doctrine->getRepository(Trick::class)->find($id);

        $form = $this->createForm(TrickType::class, $editForm);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {
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
        $trick = $doctrine->getRepository(Trick::class)->find($id);


        // Vérifiez si le slug passé dans l'URL correspond au slug généré à partir du titre de l'article
        $trickSlug = $slugger->slug($trick->getTitle())->lower()->toString();
        
        if ($slug !== $trickSlug) {
            // Redirigez vers l'URL avec le slug correct
            return $this->redirectToRoute('app_edit', [
                'id' => $id,
                'slug' => $trickSlug,
            ]);
        }

        // Erreur si trick n'existe pas
        if (!$trick) {
            echo "Aucun Trick n'a était récupéré";
            die();
        }


        return $this->render('edit/index.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick,
            'edit_form' => $form->createView(),
            'isConnected' => $isConnected
        ]);
    }


    /**
     ****************************************** Fonction pour Ajouter un Trick **********************
     * @Route("/trick/add", name="app_create")
     */

     public function add(Request $request, SluggerInterface $slugger): Response
    {
        // Set une variable isConnected pour verifier si un user est connecté
        // Sert à declarer ma logique dans le controller au lieu de le faire dans Twig
        $isConnected = false;
        $userConnected = $this->getUser();

        // Si un user est connecté
        if ($userConnected) {
            $isConnected = true;
        }


        $session = new Session();

        // Mon formulaire de modification de trick
        $createForm = new Trick();

        $form = $this->createForm(TrickType::class, $createForm);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
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


        return $this->render('create/index.html.twig', [
            'controller_name' => 'TrickController',
            'create_form' => $form->createView(),
            'isConnected' => $isConnected
        ]);
    }



    /**
     * ****************************************** Fonction pour Supprimer un Trick **********************
     * @Route("/trick/delete/{id}", name="app_delete")
     */
    public function delete(ManagerRegistry $doctrine, int $id): Response
    {
        // Mon formulaire de modification de trick
        $deleteTrick = $doctrine->getRepository(Trick::class)->find($id);
        
        $this->entityManager->remove($deleteTrick);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_home');

    }
}
