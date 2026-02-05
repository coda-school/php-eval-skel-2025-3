<?php

namespace App\Repository;

use App\Entity\Tweet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tweet>
 */
class TweetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tweet::class);
    }

    /**
     * Récupère les tweets pour le fil d'actualité global (ordre décroissant)
     */
    public function findLatestTweets(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.createdDate', 'DESC')
            ->setMaxResults(50) // Limite pour performance
            ->getQuery()
            ->getResult();
    }

    public function findAllMainTweets(): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.parentTweet IS NULL')
            ->andWhere('t.isDeleted = :deleted')
            ->setParameter('deleted', false)
            ->orderBy('t.createdDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPopularTweets(int $limit = 50): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.isDeleted = :deleted')
            ->setParameter('deleted', false)
            ->orderBy('t.likesCount', 'DESC')
            ->addOrderBy('t.viewsCount', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


}
