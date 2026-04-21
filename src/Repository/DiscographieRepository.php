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
     public function search(string $query): array
    {
        return $this->createQueryBuilder('d')
            ->where('d.titre LIKE :q')
            ->orWhere('d.artiste LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->orderBy('d.titre', 'ASC')
            ->getQuery()
            ->getResult();
    }
        /**
     * Retourne l'ID d'un album aléatoire via SQL RAND() (MySQL).
     * Null si la table est vide.
     */
    public function findRandomId(): ?int
    {
        $result = $this->getEntityManager()->getConnection()
            ->executeQuery('SELECT id FROM discographie ORDER BY RAND() LIMIT 1')
            ->fetchOne();

        return $result !== false ? (int) $result : null;
    }
}