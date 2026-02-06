<?php

namespace App\DataFixtures;

use App\Entity\Tweet;
use App\Entity\User;
use App\Entity\UserSettings;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Initialisation de Faker en français
        $faker = Factory::create('fr_FR');

        $users = [];

        // --- 1. ADMIN USER (Batman) ---
        $admin = new User();
        $admin->setEmail('admin@stela.com')
            ->setUsername('Batman')
            ->setDisplayName('Le Chevalier Noir')
            ->setPassword($this->hasher->hashPassword($admin, 'password'))
            ->setRoles(['ROLE_ADMIN'])
            ->setBio('Je suis la nuit. Je suis le code. Le Scribe veille.')
            ->setIsVerified(true)
            ->setAvatar('https://ui-avatars.com/api/?name=Batman&background=000&color=fff&size=128')
            ->setCreatedDate(new DateTime('-2 years'));

        $manager->persist($admin);
        $users[] = $admin;

        // Settings Admin
        $adminSettings = new UserSettings();
        $adminSettings->setOwner($admin)
            ->setTheme('dark')
            ->setLanguage('fr')
            ->setNotificationsEnabled(true)
            ->setIsPrivateAccount(false);

        $manager->persist($adminSettings);


        // --- 2. UTILISATEURS NORMAUX (Les Fidèles) ---
        for ($i = 0; $i < 20; $i++) {
            $user = new User();

            // On génère des données distinctes pour le Nom et l'Éponyme
            $firstName = $faker->firstName();
            $lastName = $faker->lastName();
            $displayName = $firstName . ' ' . $lastName;
            $username = strtolower($firstName . $lastName . mt_rand(10, 99));

            $user->setEmail($faker->unique()->email())
            ->setUsername($username)
                ->setDisplayName($displayName)
                ->setPassword($this->hasher->hashPassword($user, 'password'))
                ->setBio($faker->realText(80))
                ->setIsVerified($faker->boolean(10)); // 10% de certifiés

            // Avatar basé sur le Nom d'affichage
            $user->setAvatar('https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=random&color=fff&size=128')
                ->setCreatedDate($faker->dateTimeBetween('-1 year'));

            $manager->persist($user);
            $users[] = $user;

            // Settings Utilisateur
            $settings = new UserSettings();
            $settings->setOwner($user)
                ->setTheme($faker->randomElement(['light', 'dark']))
                ->setLanguage($faker->randomElement(['fr', 'en']))
                ->setNotificationsEnabled($faker->boolean(80))
                ->setIsPrivateAccount($faker->boolean(10));

            $manager->persist($settings);
        }

        // --- 3. STÈLES (Tweets) & ALLIANCES (Follows) ---
        foreach ($users as $user) {

            // A. Création de Stèles (0 à 8 par personne)
            for ($j = 0; $j < mt_rand(0, 8); $j++) {
                $tweet = new Tweet();
                $tweet->setContent($faker->realText(mt_rand(50, 280))) // Entre 50 et 280 caractères
                ->setCreatedDate($faker->dateTimeBetween('-6 months'))
                    ->setAuthor($user)
                    // On simule quelques stats aléatoires pour faire vivant
                    ->setLikesCount(mt_rand(0, 50))
                    ->setViewsCount(mt_rand(50, 1000))
                    ->setRetweetCount(mt_rand(0, 10))
                    ->setReplyCount(mt_rand(0, 5));

                $manager->persist($tweet);
            }

            // B. Création du Panthéon (Followers)
            // Chaque user suit 3 à 7 autres users aléatoires
            $randomUsersToFollow = $faker->randomElements($users, mt_rand(3, 7));

            foreach ($randomUsersToFollow as $userToFollow) {
                // On ne se suit pas soi-même
                if ($user !== $userToFollow) {
                    $user->addFollowing($userToFollow);
                }
            }
        }

        $manager->flush();
    }
}
