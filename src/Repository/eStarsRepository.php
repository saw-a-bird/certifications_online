<?php

namespace App\Repository;

use App\Entity\eStars;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method eStars|null find($id, $lockMode = null, $lockVersion = null)
 * @method eStars|null findOneBy(array $criteria, array $orderBy = null)
 * @method eStars[]    findAll()
 * @method eStars[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class eStarsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, eStars::class);
    }

    public function findStar($user_id, $paper_id) {
        return $this->createQueryBuilder('s')
            ->where('s.user = :user')
            ->andWhere('s.examPaper = :paper')
            ->setParameter('user', $user_id)
            ->setParameter('paper', $paper_id)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return eStars[] Returns an array of eStars objects
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
    public function findOneBySomeField($value): ?eStars
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
