<?php

namespace App\DataFixtures;

// Entité concerné
use App\Entity\Tricks;

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
            $tricks->setTrickTitle('Trick '.$i);
            $tricks->setTrickDescription('Description du trick '.$i);
            $tricks->setTrickGroupeTrick('Groupe '.$i);
            // Object DatetimeInterface et valeur par defaut definit en tant que CURRENT_TIMESTAMP dans mon entité
            $tricks->setTrickDateCreate(new \DateTime);
            $manager->persist($tricks);
        }

        // Comments Fixtures
        $manager->flush();
    }
}
