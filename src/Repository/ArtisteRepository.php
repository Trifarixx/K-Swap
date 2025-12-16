<?php

namespace App\Repository;

use App\Entity\Artiste;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Artiste>
 */
class ArtisteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artiste::class);
    }

    // Exemple : Trouver les artistes par nom
    // public function findByNamePartial(string $name): array
    // {
    //    return $this->createQueryBuilder('a')
    //        ->andWhere('a.nomScene LIKE :val')
    //        ->setParameter('val', '%'.$name.'%')
    //        ->orderBy('a.nomScene', 'ASC')
    //        ->getQuery()
    //        ->getResult();
    // }
}