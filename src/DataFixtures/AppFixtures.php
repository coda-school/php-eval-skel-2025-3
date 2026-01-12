<?php

namespace App\DataFixtures;

use App\Entity\Tweet;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    // On récupère le hasher pour le Cryptage du mot de passe
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Initialisation de Faker
        $faker = Factory::create('fr_FR');

        // Tableau pour stocker nos users et créer des liens entre eux après
        $users = [];

        // création d'un User "Admin" pour tester facilement
        $admin = new User();
        $admin->setEmail('admin@stela.com')
            ->setUsername('Batman')
            ->setPassword($this->hasher->hashPassword($admin, 'password')) // Mot de passe : "password"
            ->setRoles(['ROLE_ADMIN'])
            ->setBio('Je suis la nuit. Je suis le code.')
            ->setIsVerified(true)
            ->setAvatar('https://ui-avatars.com/api/?name=Batman&background=000&color=fff')
            ->setCreatedDate(new DateTime());

        $manager->persist($admin);
        $users[] = $admin;

        // Création de 20 utilisateurs normaux
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email())
                ->setUsername($faker->userName())
                ->setPassword($this->hasher->hashPassword($user, 'password'))
                ->setBio($faker->realText(100)) // Une bio de 100 caractères max
                ->setIsVerified($faker->boolean(20)); // 20% de chance d'être vérifié

            // Avatar aléatoire via UI Avatars
            $user->setAvatar('https://ui-avatars.com/api/?name=' . $user->getUsername() . '&background=random')
                ->setCreatedDate($faker->dateTimeBetween('-1 year'));

            $manager->persist($user);
            $users[] = $user;
        }

        // Création des Stelas (Tweets) et des Abonnements (Follows)
        foreach ($users as $user) {

            // A. Chaque user poste entre 0 et 5 tweets
            for ($j = 0; $j < mt_rand(0, 5); $j++) {
                $tweet = new Tweet();
                $tweet->setContent($faker->realText(140)) // Contenu style Twitter
                ->setCreatedDate($faker->dateTimeBetween('-6 months')) // Date aléatoire
                ->setAuthor($user);

                $manager->persist($tweet);
            }

            // B. Chaque user suit quelques autres users aléatoires
            // On prend 3 à 5 utilisateurs au hasard dans la liste et on les suit
            $randomUsersToFollow = $faker->randomElements($users, mt_rand(3, 5));

            foreach ($randomUsersToFollow as $userToFollow) {
                // On ne se suit pas soi-même
                if ($user !== $userToFollow) {
                    $user->addFollowing($userToFollow);
                }
            }
        }

        // Envoi de toutes nos fixtures en base de données
        $manager->flush();
    }
}
