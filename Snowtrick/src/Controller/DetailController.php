<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

// Appel de mon entity pour pouvoir utiliser ces method
use App\Entity\Tricks;
use App\Entity\Comments;
use App\Entity\User;
// Transmission de mon formulaire comment
use App\Form\CommentFormType;
// Soumission du formulaire et persistance des données dans la BDD
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;


class DetailController extends AbstractController
{

    private $twig;
    private $entityManager;
        
        
    public function __construct(Environment $twig, EntityManagerInterface $entityManager) {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/detail/{id}", name="app_detail")
     */
    public function index(ManagerRegistry $doctrine, int $id, Request $request): Response
    {

        // Mon formulaire de creation de commentaires
        $commentForm = new Comments();

        $form = $this->createForm(CommentFormType::class, $commentForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupérer l'id du trick et l'attribuer au champs trick du form
            $getIdTrick = $doctrine->getRepository(Tricks::class)->find($id);
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
        $trick = $doctrine->getRepository(Tricks::class)->find($id);

        // Récuperer tous les commentaires ou trick_id correspond à l'id du trick en question sur la page detail
        $comments = $doctrine->getRepository(Comments::class)->findBy([
            'trick' => $id
        ]);


        // Erreur si trick n'existe pas
        if (!$trick) {
            echo "Aucun Trick n'a était récupéré";
            die();
        }

        // Envoyer mes donnée a ma view
        return $this->render('detail/index.html.twig', [
            'controller_name' => 'DetailController',
            'trick' => $trick,
            'comments' => $comments,
            'comment_form' => $form->createView()
        ]);
    }
    
}
