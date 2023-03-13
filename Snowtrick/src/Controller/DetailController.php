<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Appel de mon entity pour pouvoir utiliser ces method
use App\Entity\Tricks;

class DetailController extends AbstractController
{
    /**
     * @Route("/detail/{id}", name="app_detail")
     */
    public function index(ManagerRegistry $doctrine, int $id): Response
    {
        $trick = $doctrine->getRepository(Tricks::class)->find($id);
        
        if (!$trick) {
            echo "Aucun Trick n'a était récupéré";
            die();
        }

        return $this->render('detail/index.html.twig', [
            'controller_name' => 'DetailController',
            'trick' => $trick
        ]);
    }
}
