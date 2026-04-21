<?php

namespace App\Repository;

use App\Entity\Groupe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Groupe>
 */
class GroupeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Groupe::class);
    }
        /**
     * Retourne l'ID d'un groupe aléatoire via SQL RAND() (MySQL).
     * Null si la table est vide.
     */
    public function findRandomId(): ?int
    {
        $result = $this->getEntityManager()->getConnection()
            ->executeQuery('SELECT id FROM groupe ORDER BY RAND() LIMIT 1')
            ->fetchOne();

        return $result !== false ? (int) $result : null;
    }
}