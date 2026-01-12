<?php

namespace App\Controller\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
final class FollowController extends AbstractController
{
    #[Route('/{_locale}/user/{id}/follow', name: 'user_follow', requirements: ['_locale' => 'en|fr'])]
    #[Route('/user/{id}/follow', name: 'user_follow_redirect', methods: ['POST'])]
    public function follow(
        User $userToFollow,
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        //Vérifier que ce n'est pas un suivi récurssif (Un user ne peut se suivre lui même)
        if($currentUser->getId() === $userToFollow->getId()) {
            return $this->redirect($request->headers->get('referer') ?? '/');
        }

        //Double logique pour vérifier si il suit déjà le ..toFollow et dans ce cas inverser l'état du suivi
        if($currentUser->getFollowing()->contains($userToFollow)){
            $currentUser->removeFollowing($userToFollow); //retirer de la liste des suivis
        } else {
            $currentUser->addFollowing($userToFollow);
        }

        //Sauvegarder
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer') ?? '/');
    }
}
