<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Tweet;
use App\Form\TweetType;
use App\Repository\LikeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TweetController extends AbstractController
{
    #[Route('/{_locale}/tweet/{id}/like', name: 'app_tweet_like', requirements: ['_locale' => 'en|fr'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function like(Tweet $tweet, LikeRepository $likeRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $existingLike = $likeRepository->findOneBy(['tweet' => $tweet, 'user' => $user]);

        if ($existingLike) {
            $em->remove($existingLike);
            $newCount = max(0, $tweet->getLikesCount() - 1);
            $tweet->setLikesCount($newCount);
            $message = 'removed';
        } else {
            $like = new Like();
            $like->setTweet($tweet);
            $like->setUser($user);
            $em->persist($like);
            $tweet->setLikesCount($tweet->getLikesCount() + 1);
            $message = 'added';
        }

        $em->flush();

        return $this->json(['message' => $message, 'likes' => $tweet->getLikesCount()]);
    }

    #[Route('/{_locale}/tweet/{id}/view', name: 'app_tweet_view', requirements: ['_locale' => 'en|fr'], methods: ['POST'])]
    public function view(Tweet $tweet, EntityManagerInterface $em, Request $request): JsonResponse
    {
        $session = $request->getSession();
        $key = 'viewed_tweet_' . $tweet->getId();

        if (!$session->has($key) && $this->getUser() !== $tweet->getAuthor()) {
            $tweet->incrementViews();
            $em->flush();
            $session->set($key, true);
        }
        return $this->json(['views' => $tweet->getViewsCount()]);
    }

    #[Route('/{_locale}/tweet/{id}', name: 'app_tweet_show', requirements: ['_locale' => 'en|fr'], methods: ['GET'])]
    public function show(Tweet $tweet): Response
    {
        return $this->render('tweet/show.html.twig', [
            'tweet' => $tweet,
        ]);
    }
    #[Route('/{_locale}/tweet/{id}/edit', name: 'app_tweet_edit', requirements: ['_locale' => 'en|fr'], methods: ['GET', 'POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Request $request, Tweet $tweet, EntityManagerInterface $entityManager, TranslatorInterface $translator, SluggerInterface $slugger): Response
    {
        if ($tweet->getAuthor() !== $this->getUser()) {
            $this->addFlash('error', $translator->trans('errors.edit_denied', [], 'stela'));
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(TweetType::class, $tweet);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                if ($form->has('deleteImage')) {
                    $deleteImage = $form->get('deleteImage')->getData();
                    if ($deleteImage && $tweet->getImageFilename()) {
                        $oldFile = $this->getParameter('tweets_directory').'/'.$tweet->getImageFilename();
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                        $tweet->setImageFilename(null);
                    }
                }

                $imageFile = $form->get('imageFile')->getData();
                if ($imageFile) {
                    if ($tweet->getImageFilename()) {
                        $oldFile = $this->getParameter('tweets_directory') . '/' . $tweet->getImageFilename();
                        if (file_exists($oldFile)) {
                            unlink($oldFile);
                        }
                    }

                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                    try {
                        $imageFile->move($this->getParameter('tweets_directory'), $newFilename);
                        $tweet->setImageFilename($newFilename);
                    } catch (Exception $e) {
                        $this->addFlash('error', 'Erreur image : ' . $e->getMessage());
                    }
                }

                $entityManager->flush();
                $this->addFlash('success', $translator->trans('notifications.tweet_updated', [], 'stela'));
                return $this->redirectToRoute('app_home');
            } else {
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
        }

        return $this->render('tweet/edit.html.twig', [
            'tweet' => $tweet,
            'form' => $form,
        ]);
    }

    #[Route('/{_locale}/tweet/{id}/delete', name: 'app_tweet_delete', requirements: ['_locale' => 'en|fr'], methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Tweet $tweet, EntityManagerInterface $em, Request $request, TranslatorInterface $translator): Response
    {
        if ($tweet->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$tweet->getId(), $request->request->get('_token'))) {
            $tweet->softDelete($this->getUser());

            if ($tweet->getAuthor()) {
                $tweet->getAuthor()->removeTweet($tweet);
            }

            $em->flush();
            $this->addFlash('success', $translator->trans('notifications.tweet_deleted', [], 'stela'));
        } else {
            $this->addFlash('error', 'Echec de sécurité (Token Invalide)');
        }

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_home', ['_locale' => $request->getLocale()]));
    }
}
