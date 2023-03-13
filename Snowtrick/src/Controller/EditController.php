<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Appel de mon entity pour pouvoir utiliser ces method
use App\Entity\Tricks;


class EditController extends AbstractController
{
    /**
     * @Route("/edit/{id}", name="app_edit")
     */
    public function index(ManagerRegistry $doctrine, int $id): Response
    {

        //Permet de recuperer mes données en BDD grace a mes method du Repository et de Doctrine ORM
        $trick = $doctrine->getRepository(Tricks::class)->find($id);

        if (!$trick) {
            echo "Aucun Trick n'a était récupéré";
            die();
        }

        return $this->render('edit/index.html.twig', [
            'controller_name' => 'EditController',
            'trick' => $trick
        ]);
    }

}
