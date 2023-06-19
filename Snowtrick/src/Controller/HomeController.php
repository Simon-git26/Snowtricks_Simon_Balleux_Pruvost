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

        // Savoir si le user est authentifié ou non
        $userConnected = null !== $this->getUser();

        $idUserConnected="";
        
        if ($userConnected) {
            // Recuperer l'id de mon user connecté pour ma condition dans twig pour afficher stylo et corbeille sur les bon id de trick
            $idUserConnected = $this->getUser()->getId();
        }

        //Permet de recuperer mes données en BDD grace a mes method du Repository et de Doctrine ORM
        $tricks = $doctrine->getRepository(Trick::class)->findAll();
    

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'idUserConnected' => $idUserConnected
        ]);
    }
}
