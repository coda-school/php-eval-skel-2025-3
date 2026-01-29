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

    // Note: Pour les tweets d'un user spécifique, $user->getTweets() suffit
    // si on ajoute l'annotation @ORM\OrderBy({"createdDate" = "DESC"}) dans l'entité User sur la relation OneToMany
}
