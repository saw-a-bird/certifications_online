<?php

namespace App\Repository;

use App\Entity\Enrolled;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Enrolled|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enrolled|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enrolled[]    findAll()
 * @method Enrolled[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EnrolledRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Enrolled::class);
    }

    // /**
    //  * @return Enrolled[] Returns an array of Enrolled objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Enrolled
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
