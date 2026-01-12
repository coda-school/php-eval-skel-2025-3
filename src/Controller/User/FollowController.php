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
    #[Route('/{_locale}/user/{id}/follow', name: 'app_user_follow', requirements: ['_locale' => 'en|fr'])]
    #[Route('/user/{id}/follow', name: 'app_user_follow_redirect', methods: ['POST'])]
    public function follow(
        User $userToFollow,
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if($currentUser->getId() === $userToFollow->getId()) {
            return $this->redirect($request->headers->get('referer') ?? '/');
        }

        if($currentUser->getFollowing()->contains($userToFollow)){
            $currentUser->removeFollowing($userToFollow);
        } else {
            $currentUser->addFollowing($userToFollow);
        }

        //Sauvegarder
        $entityManager->flush();

        return $this->redirect($request->headers->get('referer') ?? '/');
    }
}
