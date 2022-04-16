<?php

namespace App\Repository;

use App\Entity\CertificationRates;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CertificationRates|null find($id, $lockMode = null, $lockVersion = null)
 * @method CertificationRates|null findOneBy(array $criteria, array $orderBy = null)
 * @method CertificationRates[]    findAll()
 * @method CertificationRates[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CertificationRatesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CertificationRates::class);
    }

    // /**
    //  * @return CertificationRates[] Returns an array of CertificationRates objects
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
    public function findOneBySomeField($value): ?CertificationRates
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
