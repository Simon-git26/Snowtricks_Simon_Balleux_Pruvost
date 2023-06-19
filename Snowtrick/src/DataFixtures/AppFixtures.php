<?php

namespace App\DataFixtures;

// Entité concerné
use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\User;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

// Hachage de mes passwords
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 2; $i++) {
            // Instanciations de ma class Trick
            $users = new User();
            $users->setUsername('Simon'.$i);
            $users->setEmail('simoncestmoi@hotmail.fr'.$i);
            $users->setPassword('admintest'.$i);
            $users->setFirstname('balleux'.$i);
            $users->setImagePath('image path');
            $users->setImagePath('image path');
            $users->setRoles(['ROLES_USER']);
            $users->setIsVerified(1);
            $manager->persist($users);
            $this->setTricks($users, $manager);
        }
        $manager->flush();
    }

    private function setTricks(User $user, ObjectManager $manager) {
        // Trick Fixtures
        for ($i = 1; $i <= 3; $i++) {
            // Instanciations de ma class Trick
            $tricks = new Trick();
            $tricks->setUser($user);
            $tricks->setTitle('Trick '.$i);
            $tricks->setDescription('Description du trick '.$i);
            $tricks->setGroupe('Groupe '.$i);
            $tricks->setSlug(urlencode($tricks->getTitle()));
            // Object DatetimeInterface et valeur par defaut definit en tant que CURRENT_TIMESTAMP dans mon entité
            $tricks->setDateCreate(new \DateTime);
            $manager->persist($tricks);
            $this->setComments($user, $tricks, $manager);
        }
    }

    private function setComments(User $user, Trick $trick, ObjectManager $manager) {
        // Comment Fixtures
        for ($i = 1; $i <= 2; $i++) {
            // Instanciations de ma class Trick
            $comments = new Comment();
            $comments->setUser($user);
            $comments->setTrick($trick);
            $comments->setContent('Commentaire '.$i);
            $comments->setDateCreate(new \DateTime);
            $comments->setIsActif(1);
            $manager->persist($comments);
        }
    }
}
