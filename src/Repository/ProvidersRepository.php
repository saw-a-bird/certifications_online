<?php

namespace App\Repository;

use App\Entity\Providers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Providers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Providers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Providers[]    findAll()
 * @method Providers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProvidersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Providers::class);
    }

    // /**
    //  * @return Providers[] Returns an array of Providers objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Providers
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
