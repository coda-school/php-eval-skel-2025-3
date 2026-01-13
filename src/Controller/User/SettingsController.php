<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\UserSettings;
use App\Form\SettingsType;
use App\Repository\UserSettingsRepository;
use App\Service\UserSettingsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class SettingsController extends AbstractController
{
    #[Route('/{_locale}/settings', name: 'app_settings', requirements: ['_locale' => 'en|fr'])]
    public function index(
        Request                $request,
        UserSettingsRepository $settingsRepo,
        UserSettingsService    $settingsService
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
        }

        $form = $this->createForm(SettingsType::class, $userSettings);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $avatarFile = $form->get('avatarFile')->getData();

            $settingsService->handleSave($userSettings, $avatarFile);

            $this->addFlash('success', 'Vos paramètres ont été mis à jour avec succès !');

            return $this->redirectToRoute('app_settings', ['_locale' => $request->getLocale()]);
        }

        return $this->render('settings/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
