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
use DateTime;

class TrickController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }


    /**
    * @Route("/trick/detail/{slug}", name="app_detail")
    */
    public function detail(Request $request): Response
    {
        // Récupérer le trick en utilisant le slug
        $slug = $request->get('slug');
        $selectedTrick = $this->getDoctrine()->getRepository(Trick::class)->findOneBy(['slug' => urlencode($slug)]);

        // Vérifier si le trick existe
        if (!$selectedTrick) {
            throw $this->createNotFoundException('Trick non trouvé !');
        }

        /****************** Partie Commentaire ********************/ 
        $comments = $selectedTrick->getComments();

        // Mon formulaire de création de commentaires
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        /* Lorsque le formulaire est soumis et valide */
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setDateCreate(new DateTime())->setUser($this->getUser());
            $selectedTrick->addComment($comment);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }  
        /****************** Fin Partie Commentaire ********************/ 

        // Envoyer mes données à ma vue
        return $this->render('detail/index.html.twig', [
            'trick' => $selectedTrick,
            'comment_form' => $form->createView(),
            'comments' => $comments
        ]);
    }


    /**
     * ******************************************* Fonction pour Editer un Trick **********************
     * @Route("/trick/edit/{id}/{slug}", name="app_edit")
     */
    public function edit(ManagerRegistry $doctrine, int $id, $slug, SluggerInterface $slugger, Request $request): Response
    {

        // Savoir si le user est authentifié ou non
        $userConnected = null !== $this->getUser();

        if ($userConnected) {
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
                'edit_form' => $form->createView()
            ]);
        }

        
    }


    /**
     ****************************************** Fonction pour Ajouter un Trick **********************
     * @Route("/trick/add", name="app_create")
     */

    public function add(Request $request, SluggerInterface $slugger): Response
    {
        // Set une variable isConnected pour verifier si un user est connecté
        $userConnected = null !== $this->getUser();

        if ($userConnected) {
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


            return $this->render('create/index.html.twig', [
                'create_form' => $form->createView()
            ]);
        }


        
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
