<?php

namespace App\Controller;

use App\Entity\Tweet;
use App\Form\TweetType;
use App\Repository\TweetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_USER')]
class HomeController extends AbstractController
{
    #[Route('/{_locale}/home', name: 'app_home', requirements: ['_locale' => 'en|fr'], defaults: ['_locale' => 'fr'])]
    public function index(
        Request                $request,
        TweetRepository        $tweetRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface       $slugger,
        TranslatorInterface    $translator
    ): Response
    {
        $tweet = new Tweet();
        $form = $this->createForm(TweetType::class, $tweet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tweet->setAuthor($this->getUser());
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $imageFile->move($this->getParameter('tweets_directory'), $newFilename);
                    $tweet->setImageFilename($newFilename);
                } catch (FileException) {
                    $this->addFlash('error', 'Erreur lors de la gravure de l\'image.');
                }
            }

            $entityManager->persist($tweet);
            $entityManager->flush();

            $this->addFlash('success', $translator->trans('notifications.tweet_created', [], 'stela'));

            return $this->redirectToRoute('app_home');
        }

        $tweets = $tweetRepository->findAllMainTweets();

        return $this->render('home/index.html.twig', [
            'tweets' => $tweets,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{_locale}/trends', name: 'app_tweet_popular', requirements: ['_locale' => 'en|fr'], methods: ['GET'])]
    public function popular(TweetRepository $tweetRepository): Response
    {
        $tweets = $tweetRepository->findPopularTweets(50);

        return $this->render('home/index.html.twig', [
            'tweets' => $tweets,
            'form' => null,
        ]);
    }

    #[Route('/', name: 'app_home_redirect')]
    public function redirectNoLocale(): Response
    {
        return $this->redirectToRoute('app_home', ['_locale' => 'fr']);
    }
}
