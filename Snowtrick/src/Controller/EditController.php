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



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// Personalisation de mon formulaire
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


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
        $editForm = $doctrine->getRepository(Tricks::class)->find($id);

        $form = $this->createFormBuilder($editForm)
        ->add('title')
        ->add('description')
        ->add('groupe')
        ->add('image')
        ->add('video')
        // Bouton submit
        ->add('submit', SubmitType::class)
        ->getForm();


        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Attribué une valeur Datetime a mon champs date
            $date= new \DateTime;
            $editForm->setDateCreate($date);

            // Récupérer l'id du user connecté et l'attribuer au champs user
            $userConnected = $this->getUser();
            $editForm->setUser($userConnected);


            $this->entityManager->persist($editForm);
            $this->entityManager->flush();
            

            // Changer la route plus tard pour /detail/{id}
            return $this->redirectToRoute('app_home');
        }


        return $this->render('edit/index.html.twig', [
            'controller_name' => 'EditController',
            'edit_form' => $form->createView()
        ]);
    }

}
