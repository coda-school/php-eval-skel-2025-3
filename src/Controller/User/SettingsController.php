<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\UserSettings;
use App\Form\SettingsType;
use App\Repository\UserSettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire; // Pour le dossier upload
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SettingsController extends AbstractController
{
    #[Route('/{_locale}/settings', name: 'app_user_settings', requirements: ['_locale' => 'en|fr'], methods: ['POST', 'GET'])]
    #[Route('/settings', name: 'app_user_settings_redirect', methods: ['POST', 'GET'])]
    public function index(
        Request                $request,
        UserSettingsRepository $settingsRepo,
        EntityManagerInterface $entityManager, // On appelle l'EntityManager directement
        SluggerInterface       $slugger,
        TranslatorInterface    $translator,
        #[Autowire('%kernel.project_dir%/public/uploads/avatars')] string $avatarsDirectory // Injection du dossier
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userSettings = $settingsRepo->findOneBy(['owner' => $user]);

        if (!$userSettings) {
            $userSettings = new UserSettings();
            $userSettings->setOwner($user);
            $userSettings->setTheme('light');
            $userSettings->setNotificationsEnabled(true);
            $userSettings->setIsPrivateAccount(false);
            $userSettings->setLanguage($request->getLocale());
        }

        $form = $this->createForm(SettingsType::class, $userSettings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // --- 1. GESTION DE L'AVATAR (Code rapatrié du Service) ---
            $avatarFile = $form->get('avatarFile')->getData();
            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $avatarFile->guessExtension();

                try {
                    $avatarFile->move($avatarsDirectory, $newFilename);
                    $user->setAvatar('uploads/avatars/' . $newFilename);
                } catch (FileException $e) {
                    // Tu pourrais ajouter un flash error ici si tu veux
                }
            }

            // --- 2. SAUVEGARDE EXPLICITE ---
            // On dit à Doctrine de surveiller les deux entités
            $entityManager->persist($userSettings);
            $entityManager->persist($user); // <--- C'EST CA QUI SAUVEGARDE LE NOM !

            $entityManager->flush();

            $this->addFlash('success', $translator->trans('notifications.settings_saved', [], 'stela'));

            // Redirection selon la langue choisie
            $newLocale = $userSettings->getLanguage();
            if (!in_array($newLocale, ['en', 'fr'])) {
                $newLocale = 'fr';
            }

            return $this->redirectToRoute('app_user_settings', ['_locale' => $newLocale]);
        }

        return $this->render('settings/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
