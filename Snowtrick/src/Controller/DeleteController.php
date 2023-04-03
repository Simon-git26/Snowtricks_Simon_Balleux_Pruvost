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

// Personalisation de mon formulaire
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class DeleteController extends AbstractController
{

    private $twig;
    private $entityManager;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager) {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/delete/{id}", name="app_delete")
     */
    public function index(ManagerRegistry $doctrine, int $id, Request $request): Response
    {


        // Mon formulaire de modification de trick
        $deleteTrick = $doctrine->getRepository(Tricks::class)->find($id);

        $form = $this->createFormBuilder($deleteTrick)
       
        // Bouton submit
        ->add('submit', SubmitType::class, [
            'label' => 'Supprimer ce Trick ?',
        ])

        ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->remove($deleteTrick);
            $this->entityManager->flush();
            
            // Changer la route plus tard pour /detail/{id}
            return $this->redirectToRoute('app_home');
        }




        //Permet de recuperer mes données en BDD grace a mes method du Repository et de Doctrine ORM
        $trick = $doctrine->getRepository(Tricks::class)->find($id);

        if (!$trick) {
            echo "Aucun Trick n'a était récupéré";
            die();
        }

        return $this->render('delete/index.html.twig', [
            'controller_name' => 'DeleteController',
            'trick' => $trick,
            'delete_trick' => $form->createView()
        ]);
    }

}
