<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Appel de mon entity pour pouvoir utiliser ces method
use App\Entity\Tricks;


class DeleteController extends AbstractController
{
    /**
     * @Route("/delete/{id}", name="app_delete")
     */
    public function index(ManagerRegistry $doctrine, int $id): Response
    {

        //Permet de recuperer mes données en BDD grace a mes method du Repository et de Doctrine ORM
        $trick = $doctrine->getRepository(Tricks::class)->find($id);

        if (!$trick) {
            echo "Aucun Trick n'a était récupéré";
            die();
        }

        return $this->render('delete/index.html.twig', [
            'controller_name' => 'DeleteController',
            'trick' => $trick
        ]);
    }

}
