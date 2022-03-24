<?php

namespace App\Repository;

use App\Entity\Certifications;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Certifications|null find($id, $lockMode = null, $lockVersion = null)
 * @method Certifications|null findOneBy(array $criteria, array $orderBy = null)
 * @method Certifications[]    findAll()
 * @method Certifications[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CertificationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Certifications::class);
    }

    public function byTitle($str) {
        return $this->createQueryBuilder('u')
            ->andWhere('u.title like :str')
            ->orderBy('u.creation_date', 'desc')
            ->setParameter('str','%'.$str.'%')
            ->getQuery()
            ->getResult();
    }

    public function byProvider($str) {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.provider', 'p')
            ->andWhere('p.name = :str')
            ->orderBy('u.creation_date', 'desc')
            ->setParameter('str', $str)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return Certifications[] Returns an array of Certifications objects
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
    public function findOneBySomeField($value): ?Certifications
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
