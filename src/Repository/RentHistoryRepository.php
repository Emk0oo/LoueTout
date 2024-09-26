<?php

namespace App\Repository;

use App\Entity\RentHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RentHistory>
 */
class RentHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RentHistory::class);
    }

    public function isReservationOngoing(\DateTime $startDate, \DateTime $endDate): bool
    {
        $qb = $this->createQueryBuilder('r')
            ->where('r.started_at < :endDate')
            ->andWhere('r.ended_at > :startDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery();

        return (bool) $qb->getOneOrNullResult(); // retourne true si une réservation est trouvée
    }
    
//    /**
//     * @return RentHistory[] Returns an array of RentHistory objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?RentHistory
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
