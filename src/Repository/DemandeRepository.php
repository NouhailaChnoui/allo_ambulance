<?php

namespace App\Repository;

use App\Entity\Demande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class DemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Demande::class);
    }


    public function findWithFilters(?string $statut = null, ?string $type = null, ?string $dateDebut = null, ?string $dateFin = null)
{
    $qb = $this->createQueryBuilder('d')
        ->leftJoin('d.client', 'c')
        ->orderBy('d.createdAt', 'DESC');

    if ($statut) {
        $qb->andWhere('d.statut = :statut')
           ->setParameter('statut', $statut);
    }

    if ($type) {
        $qb->andWhere('d.type = :type')
           ->setParameter('type', $type);
    }

    if ($dateDebut) {
        $qb->andWhere('d.createdAt >= :dateDebut')
           ->setParameter('dateDebut', new \DateTime($dateDebut . ' 00:00:00'));
    }

    if ($dateFin) {
        $qb->andWhere('d.createdAt <= :dateFin')
           ->setParameter('dateFin', new \DateTime($dateFin . ' 23:59:59'));
    }

    return $qb->getQuery()->getResult();
}
}
