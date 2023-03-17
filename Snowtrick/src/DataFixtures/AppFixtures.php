<?php

namespace App\DataFixtures;

// Entité concerné
use App\Entity\Tricks;
// use App\Entity\Comments;
//use App\Entity\User;

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

            // $this->setComments($tricks, $manager);
            //$this->setUser($user, $manager);
        }
        $manager->flush();
    }

    /*
    private function setComments(Tricks $trick, ObjectManager $manager) {
        // Comments Fixtures
        for ($i = 1; $i <= 5; $i++) {
            // Instanciations de ma class Trick
            $comments = new Comments();
            $comments->setTrick($trick);
            $comments->setContent('Commentaire '.$i);
            $comments->setDateCreate(new \DateTime);
            $comments->setIsActif(1);

            $manager->persist($comments);
        }
    }
    */

    /*
    private function setUser(User $user, ObjectManager $manager) {
        // Comments Fixtures
        for ($i = 1; $i <= 5; $i++) {
            // Instanciations de ma class Trick
            $user = new User();
            $user->setUser($user);
            $user->setContent('Commentaire '.$i);
            $user->setDateCreate(new \DateTime);
            $user->setIsActif(1);

            $manager->persist($user);
        }
    }
    */
}
