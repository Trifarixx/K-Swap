<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Avis>
 */
class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    /**
     * Exemple : Récupérer la note moyenne d'un album
     */
    public function getAverageNoteForAlbum(int $discographieId): ?float
    {
        return $this->createQueryBuilder('a')
            ->select('AVG(a.note) as avgNote')
            ->andWhere('a.discographie = :id')
            ->setParameter('id', $discographieId)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findFeed(int $page = 1, int $limit = 5): Paginator
    {
        $query = $this->createQueryBuilder('a')
            ->addSelect('u', 'd', 'ar')
            ->leftJoin('a.user', 'u')
            ->leftJoin('a.discographie', 'd')
            ->leftJoin('d.artiste', 'ar')
            ->orderBy('a.dateCreation', 'DESC')
            ->addOrderBy('a.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($query);
    }
}
