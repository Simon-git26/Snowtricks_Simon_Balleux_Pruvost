<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Appel de mon entity pour pouvoir utiliser ces method
use App\Entity\Comment;
// Soumission du formulaire et persistance des données dans la BDD
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
// Personalisation de mon formulaire
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class CommentController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/comment/delete/{id}", name="app_delete_comment")
     */
    public function delete(ManagerRegistry $doctrine, int $id, Request $request): Response
    {

        // Pas besoin de check si user existe car route securisé avec IS_AUTHENTICATED_FULLY
        
        // Mon formulaire de modification de trick
        $deleteComment = $doctrine->getRepository(Comment::class)->find($id);

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
        $comment = $doctrine->getRepository(Comment::class)->find($id);

        if (!$comment) {
            echo "Aucun commentaire n'a était récupéré";
            die();
        }

        return $this->render('Comment/delete_comment/index.html.twig', [
            'controller_name' => 'CommentController',
            'comment' => $comment,
            'delete_comment' => $form->createView()
        ]);
    }
}
