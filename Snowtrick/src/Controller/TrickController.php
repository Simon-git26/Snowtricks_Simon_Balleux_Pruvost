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

//Sotcker mon message en session
use Symfony\Component\HttpFoundation\Session\Session;
// Transmission de mon formulaire comment
use App\Form\CommentType;
use App\Entity\Comment;
use App\Entity\Images;
use App\Entity\Videos;
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
    public function edit(Trick $trick, Request $request): Response
    {
        // Pas besoin de check si user existe car route securisé avec IS_AUTHENTICATED_FULLY 

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            //dd($form->getData()); // dd($form->getErrors()); // dd($form->getErrors(true));
            

            // Video uploader //
            $videos = $form->get('videos')->getData();
            $arrayVideo = explode(';', $videos);
            
            // Je boucle sur l'explode des videos'
            foreach($arrayVideo as $video) {
                // Nouvelle instance de videos
                $vid = new Videos();
                $vid->setLien($video);

                // Ajouter la video
                $trick->addVideo($vid);
            }


            // Image uploader //
            /* Recuperer ma propriete image */
            $images = $form->get('images')->getData();

            // Je boucle sur les images recu
            foreach($images as $image) {
                // Je genere un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // Je copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('image_user'),
                    $fichier
                );


                // Stocker le nom de l'image dans bdd
                // Nouvelle instance de images
                $img = new Images();
                $img->setName($fichier);
                // Ajouter l'image
                $trick->addImage($img);
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
    public function add(Request $request): Response
    {
        // Pas besoin de check si user existe car route securisé avec IS_AUTHENTICATED_FULLY
     
        $session = new Session();

        // Mon formulaire de modification de trick
        $createForm = new Trick();

        $form = $this->createForm(TrickType::class, $createForm);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            //dd($form->getData()); // dd($form->getErrors()); // dd($form->getErrors(true));
            if($form->isValid()) {  

                // Attribué la date de la creation
                $date= new \DateTime;
                $createForm->setDateCreate($date);

                // Récupérer l'id du user connecté
                $userConnected = $this->getUser();
                $createForm->setUser($userConnected);
                

                // Video uploader //
                $videos = $form->get('videos')->getData();
                $arrayVideo = explode(';', $videos);
                
                // Je boucle sur l'explode des videos recu
                foreach($arrayVideo as $video) {
                 
                    // Nouvelle instance de video
                    $vid = new Videos();
                    $vid->setLien($video);

                    // Ajouter la video
                    $createForm->addVideo($vid);
                }


                // Image uploader //
                /* Recuperer ma propriete image */
                $images = $form->get('images')->getData();

                // Je boucle sur les images recu
                foreach($images as $image) {
                    // Je genere un nouveau nom de fichier
                    $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                    // Je copie le fichier dans le dossier uploads
                    $image->move(
                        $this->getParameter('image_user'),
                        $fichier
                    );

                    // Stocker le nom de l'image dans bdd
                    // Nouvelle instance de images
                    $img = new Images();
                    $img->setName($fichier);

                    // Ajouter l'image
                    $createForm->addImage($img);
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
        // Mon formulaire de modification de trick
        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_home');

    }


    /**
     * @Route("/trick/delete/image/{id}", name="app_delete_image")
     */
    public function deleteImage(Images $images): Response
    {   
        $this->entityManager->remove($images);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    
    /**
     * @Route("/trick/delete/video/{id}", name="app_delete_video")
     */
    public function deleteVideo(Videos $videos): Response
    {   
        $this->entityManager->remove($videos);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_home');
    }
}
