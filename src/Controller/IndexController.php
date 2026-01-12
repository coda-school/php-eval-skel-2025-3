<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    /**
     * Gère l'accès à la racine pure "/"
     */
    #[Route('/')]
    public function indexNoLocale(Request $request): RedirectResponse
    {
        // 1. Détection de la langue (Navigateur ou défaut 'fr')
        $locale = $request->getPreferredLanguage(['fr', 'en']) ?? 'fr';

        // 2. Redirection vers la page de connexion (ou 'app_home' si tu as une home)
        return $this->redirectToRoute('app_login', ['_locale' => $locale]);
    }
}
