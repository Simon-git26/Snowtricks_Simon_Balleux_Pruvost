<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
// Appel de mon entity pour pouvoir utiliser ces method
use App\Entity\Tricks;
// Transmission de mon formulaire cedit
use App\Form\EditFormType;
// Soumission du formulaire et persistance des données dans la BDD
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;



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
    public function index(ManagerRegistry $doctrine, int $id, Request $request): Response
    {

        // Mon formulaire de modification de trick
        $editForm = new Tricks();

        $form = $this->createForm(EditFormType::class, $editForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Attribué la date de la creation à mon champ date_create
            $date= new \DateTime;
            $dateCreate = $form->get('date_create')->getData();
            $dateCreate = $date;
            $editForm->setDateCreate($dateCreate);

            // Récupérer l'id du user connecté et l'attribuer au champs user du form
            $userConnected = $this->getUser();
            $idUser = $form->get('user')->getData();
            $idUser = $userConnected;
            $editForm->setUser($idUser);



            $this->entityManager->persist($editForm);
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

        return $this->render('edit/index.html.twig', [
            'controller_name' => 'EditController',
            'trick' => $trick,
            'edit_form' => $form->createView()
        ]);
    }

}
