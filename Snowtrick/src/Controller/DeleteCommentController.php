<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
// Appel de mon entity pour pouvoir utiliser ces method
use App\Entity\Comments;
// Soumission du formulaire et persistance des données dans la BDD
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

// Personalisation de mon formulaire
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DeleteCommentController extends AbstractController
{
    private $twig;
    private $entityManager;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager) {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/delete/comment/{id}", name="app_delete_comment")
     */
    public function index(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        // Mon formulaire de modification de trick
        $deleteComment = $doctrine->getRepository(Comments::class)->find($id);

        $form = $this->createFormBuilder($deleteComment)
       
        // Bouton submit
        ->add('submit', SubmitType::class, [
            'label' => 'Supprimer ce Commentaire ?',
        ])
        ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($deleteComment);
            $this->entityManager->flush();
            
            // Changer la route plus tard pour /detail/{id}
            return $this->redirectToRoute('app_home');
        }


        //Permet de recuperer mes données en BDD grace a mes method du Repository et de Doctrine ORM
        $comment = $doctrine->getRepository(Comments::class)->find($id);

        if (!$comment) {
            echo "Aucun commentaire n'a était récupéré";
            die();
        }

        return $this->render('delete_comment/index.html.twig', [
            'controller_name' => 'DeleteCommentController',
            'comment' => $comment,
            'delete_comment' => $form->createView()
        ]);
    }
}
