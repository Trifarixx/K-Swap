<?php

namespace App\Repository;

use App\Entity\Morceau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Morceau>
 */
class MorceauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Morceau::class);
    }


    public function searchByThreeCriteria(?string $nomGroupe, ?string $nomAlbum, ?string $nomMorceau)
    {
        // 'm' c'est l'alias pour Morceau
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.album', 'a') // Jointure avec l'entité Album
            ->leftJoin('a.groupe', 'g'); // Jointure avec l'entité Groupe (adapte 'groupe' si ta relation s'appelle autrement)

        // Si on a tapé un nom de groupe
        if (!empty($nomGroupe)) {
            $qb->andWhere('g.nom LIKE :groupe')
                ->setParameter('groupe', '%' . $nomGroupe . '%');
        }

        // Si on a tapé un nom d'album
        if (!empty($nomAlbum)) {
            $qb->andWhere('a.nom LIKE :album') // Adapte "nom" par "titre" selon ton entité Album
                ->setParameter('album', '%' . $nomAlbum . '%');
        }

        // Si on a tapé un nom de morceau
        if (!empty($nomMorceau)) {
            $qb->andWhere('m.titre LIKE :morceau') // Adapte "titre" par "nom" selon ton entité Morceau
                ->setParameter('morceau', '%' . $nomMorceau . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
