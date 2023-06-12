<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Appel de mon entity pour pouvoir utiliser ces method
use App\Entity\Trick;


class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        // Set une variable isConnected pour verifier si un user est connecté
        // Sert à declarer ma logique dans le controller au lieu de le faire dans Twig
        $isConnected = false;
        $userConnected = $this->getUser();

        $idUserConnected = "";

        // Si un user est connecté
        if ($userConnected) {
            $isConnected = true;

            // Recuperer l'id de mon user connecté pour ma condition dans twig pour afficher stylo et corbeille sur les bon id de trick
            $idUserConnected = $this->getUser()->getId();
        }


        //Permet de recuperer mes données en BDD grace a mes method du Repository et de Doctrine ORM
        $tricks = $doctrine->getRepository(Trick::class)->findAll();
     

      
        

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'tricks' => $tricks,
            'isConnected' => $isConnected,
            'idUserConnected' => $idUserConnected
        ]);
    }
}
