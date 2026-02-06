<?php

namespace App\Repository;

use App\Entity\Tweet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
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

    public function findFeedQuery(): Query
    {
        return $this->createQueryBuilder('t')
            ->addSelect('u')
            ->leftJoin('t.author', 'u')
            ->andWhere('t.parentTweet IS NULL')
            ->andWhere('t.isDeleted = :deleted')
            ->setParameter('deleted', false)
            ->orderBy('t.createdDate', 'DESC')
            ->getQuery();
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
