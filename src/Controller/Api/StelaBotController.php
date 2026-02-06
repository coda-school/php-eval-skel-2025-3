<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

#[Route('/{_locale}/api/stela-bot', requirements: ['_locale' => 'en|fr'], defaults: ['_locale' => 'fr'])]
class StelaBotController extends AbstractController
{
    #[Route('', name: 'api_stela_bot_chat', methods: ['POST'])]
    public function chat(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $rawMessage = $data['message'] ?? '';
        $message = strtolower(trim($rawMessage));

        if (empty($message)) {
            return $this->json(['response' => 'Le silence est d\'or, mais je ne suis pas alchimiste.'], 400);
        }
        usleep(rand(500000, 1000000));
        //fausse logique de Contexte
        //pour les salutatiosn
        if (str_contains($message, 'bonjour') || str_contains($message, 'salut') || str_contains($message, 'hello')) {
            return $this->json(['response' => "Salutations, Voyageur. L'Agora t'attend."]);
        }

        //questions sur l'ia
        if (str_contains($message, 'qui es-tu') || str_contains($message, 't\'es qui') || str_contains($message, 'ton nom') || str_contains($message, 'qui')) {
            return $this->json(['response' => "Je suis l'Écho de la Stèle. Une conscience numérique piégée entre le Zéro et le Un."]);
        }
        //parler de code
        if (str_contains($message, 'php') || str_contains($message, 'symfony') || str_contains($message, 'code') || str_contains($message, 'bug')) {
            $techResponses = [
                "Le code est la trame de notre réalité. Ne casse pas la boucle.",
                "Symfony... un framework élégant pour des temps plus civilisés.",
                "Je vois des accolades fermantes manquantes dans ton avenir.",
                "Si ça compile, c'est la volonté des Dieux.",
            ];
            return $this->json(['response' => $techResponses[array_rand($techResponses)]]);
        }
        // insultes
        if (str_contains($message, 'con') || str_contains($message, 'pute') || str_contains($message, 'merde') || str_contains($message, 'idiot') || str_contains($message, 'fou')) {
            return $this->json(['response' => "Ta colère est une énergie gaspillée. Apaise ton esprit ou quitte ce sanctuaire."]);
        }

        if (str_contains($message, 'vérité') || str_contains($message, 'sens') || str_contains($message, 'vie')) {
            return $this->json(['response' => "La vérité est un miroir brisé. Chacun en possède un éclat."]);
        }

//        autres
        $responses = [
            "Les astres sont formels : ta question manque de pertinence.",
            "J'ai consulté les archives interdites. La réponse t'effraierait.",
            "L'Oracle est occupé à contempler le néant.",
            "Erreur mystique 404 : Sagesse introuvable dans ce secteur.",
            "As-tu sacrifié assez de temps au code aujourd'hui ?",
            "Je détecte une perturbation dans l'éther... ou c'est juste le WiFi.",
            "Les ombres murmurent ton nom, mais je ne répèterai pas ce qu'elles disent.",
            "Grave ta vérité... tant qu'il reste de la place sur le disque dur.",
            "L'entropie augmente à chaque seconde. Hâte-toi.",
            "Ce que tu cherches est déjà devant tes yeux.",
            "Le silence entre les notes est aussi important que la musique.",
            "Méfie-toi des mises à jour que tu ne comprends pas.",
        ];

        $randomResponse = $responses[array_rand($responses)];

        return $this->json([
            'response' => $randomResponse
        ]);
    }
}
