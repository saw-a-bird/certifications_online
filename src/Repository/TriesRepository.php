<?php

namespace App\Repository;

use App\Entity\Tries;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tries|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tries|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tries[]    findAll()
 * @method Tries[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tries::class);
    }

    // /**
    //  * @return Tries[] Returns an array of Tries objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tries
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
