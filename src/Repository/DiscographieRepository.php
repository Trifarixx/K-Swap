<?php

namespace App\Repository;

use App\Entity\Discographie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Discographie>
 */
class DiscographieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discographie::class);
    }

    /**
     * Exemple : Récupérer les derniers albums sortis
     */
    public function findLatestReleases(int $limit = 5): array
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.dateSortie', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}