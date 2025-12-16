<?php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
}