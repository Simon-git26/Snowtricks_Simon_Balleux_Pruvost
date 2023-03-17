<?php

namespace App\DataFixtures;

// Entité concerné
use App\Entity\Tricks;
use App\Entity\Comments;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Tricks Fixtures
        for ($i = 1; $i <= 5; $i++) {
            // Instanciations de ma class Trick
            $tricks = new Tricks();
            $tricks->setTitle('Trick '.$i);
            $tricks->setDescription('Description du trick '.$i);
            $tricks->setGroupe('Groupe '.$i);
            // Object DatetimeInterface et valeur par defaut definit en tant que CURRENT_TIMESTAMP dans mon entité
            $tricks->setDateCreate(new \DateTime);
            $manager->persist($tricks);
        }

        // Comments Fixtures
        for ($i = 1; $i <= 5; $i++) {
            // Instanciations de ma class Trick
            $comments = new Comments();
            $comments->setContent('Commentaire '.$i);
            $comments->setDateCreate(new \DateTime);
            $comments->setIsActif(1);
            $manager->persist($comments);
        }

        // Comments Fixtures
        $manager->flush();
    }
}
