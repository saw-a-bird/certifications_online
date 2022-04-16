<?php

namespace App\Repository;

use App\Entity\CommentRates;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CommentRates|null find($id, $lockMode = null, $lockVersion = null)
 * @method CommentRates|null findOneBy(array $criteria, array $orderBy = null)
 * @method CommentRates[]    findAll()
 * @method CommentRates[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRatesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CommentRates::class);
    }

    // /**
    //  * @return CommentRates[] Returns an array of CommentRates objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CommentRates
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
