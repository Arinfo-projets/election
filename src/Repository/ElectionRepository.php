<?php

namespace App\Repository;

use App\Entity\Election;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Election>
 *
 * @method Election|null find($id, $lockMode = null, $lockVersion = null)
 * @method Election|null findOneBy(array $criteria, array $orderBy = null)
 * @method Election[]    findAll()
 * @method Election[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Election::class);
    }

    public function findAllForUser($userId)
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.voters', 'v')
            ->where(':userId = v.user')
            ->setParameter('userId', $userId)
            ->orderBy('e.createdAt', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function findAllForAdmin($userId)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('e.createdAt', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function getOldElection()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.isOpen = true')
            ->andWhere('e.untilAt <= CURRENT_DATE()')
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Election[] Returns an array of Election objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Election
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
