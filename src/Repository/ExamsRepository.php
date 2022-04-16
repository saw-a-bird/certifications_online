<?php

namespace App\Repository;

use App\Entity\Exams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Exams|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exams|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exams[]    findAll()
 * @method Exams[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExamsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exams::class);
    }

    // /**
    //  * @return Exams[] Returns an array of Exams objects
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
    public function findOneBySomeField($value): ?Exams
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
