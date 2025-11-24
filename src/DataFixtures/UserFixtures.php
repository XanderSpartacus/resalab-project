<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    // Ajout d'une méthode pour définir le groupe
    public static function getGroups(): array
    {
        // on charge uniquement les fixtures du groupe UserFixtures
        // pour éviter la purge de la base de données on rajoute l'option --append
        // php bin/console doctrine:fixtures:load --group=UserFixtures --append
        return ['UserFixtures']; // le nom de votre groupe
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un utilisateur standard
        $user = new User();
        $user->setEmail('user@resalab.com');
        $password = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($password);
        $user->setIsVerified(true);
        // Le rôle ROLE_USER est ajouté automatiquement par Symfony
        $manager->persist($user);

        // Création d'un utilisateur standard
        $admin = new User();
        $admin->setEmail('admin@resalab.com');
        $password = $this->hasher->hashPassword($admin, 'password');
        $admin->setPassword($password);
        $user->setIsVerified(true);
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $manager->flush();
    }
}
