<?php

namespace App\Repository;

use App\Entity\eAttempt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method eAttempt|null find($id, $lockMode = null, $lockVersion = null)
 * @method eAttempt|null findOneBy(array $criteria, array $orderBy = null)
 * @method eAttempt[]    findAll()
 * @method eAttempt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class eAttemptsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, eAttempt::class);
    }

    public function getAttempts($user_id, $paper_id)
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user_id')
            ->andWhere('a.examPaper = :paper_id')
            ->setParameter('user_id', $user_id)
            ->setParameter('paper_id', $paper_id)
            ->getQuery()
            ->getResult();
        ;
    }

    public function getLastAttempt($user_id, $paper_id)
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user_id')
            ->andWhere('a.examPaper = :paper_id')
            ->setParameter('user_id', $user_id)
            ->setParameter('paper_id', $paper_id)
            ->orderBy("id", "DESC")
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        ;
    }
    
    // /**
    //  * @return eAttempt[] Returns an array of eAttempt objects
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
    public function findOneBySomeField($value): ?eAttempt
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
