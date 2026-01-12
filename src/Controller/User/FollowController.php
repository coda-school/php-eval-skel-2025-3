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
    #[Route('/{_locale}/user/{userToFollow}/follow', name: 'app_user_follow', requirements: ['_locale' => 'en|fr'], methods: ['POST', 'GET'])]
    #[Route('/user/{userToFollow}/follow', name: 'app_user_follow_redirect', methods: ['POST'])]
    public function follow(
        User $userToFollow,
        EntityManagerInterface $entityManager,
        Request $request,
        string $_locale = 'fr'
    ): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // On ne peut pas se suivre soi-même
        if($currentUser->getId() === $userToFollow->getId()) {
            return $this->redirectToProfile($userToFollow, $_locale);
        }

        if($currentUser->getFollowing()->contains($userToFollow)){
            $currentUser->removeFollowing($userToFollow);
        } else {
            $currentUser->addFollowing($userToFollow);
        }

        $entityManager->flush();

        return $this->redirectToProfile($userToFollow, $_locale);
    }

    private function redirectToProfile(User $user, string $locale): Response
    {
        return $this->redirectToRoute('app_user_show', [
            'id' => $user->getId(),
            '_locale' => $locale
        ]);
    }
}
