<?php

namespace App\Twig;

use App\Repository\TweetRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private TweetRepository $tweetRepository;

    public function __construct(TweetRepository $tweetRepository)
    {
        $this->tweetRepository = $tweetRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_sidebar_trends', [$this, 'getTrends']),
        ];
    }

    public function getTrends(): array
    {
        return $this->tweetRepository->findPopularTweets(3);
    }
}
