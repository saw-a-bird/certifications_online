<?php

namespace App\Repository;

use App\Entity\eProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method eProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method eProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method eProvider[]    findAll()
 * @method eProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class eProvidersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, eProvider::class);
    }

    public function nameAll() {
        return $this->createQueryBuilder('u')
            ->select("u.name")
            ->orderBy('u.name', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function pfindAll() {
        return $this->createQueryBuilder('u')
            ->orderBy('u.name', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function byProvider($name) {
        return $this->createQueryBuilder('u')
            ->andWhere('u.name like :name')
            ->orderBy('u.name', 'desc')
            ->setParameter('name','%'.$name.'%');
    }

    public function byCTitle($title) {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.certifications', 'c')
            ->andWhere('c.title like :title')
            ->orderBy('u.name', 'desc')
            ->setParameter('title','%'.$title.'%');
    }

    public function byECode($code) {
        return $this->createQueryBuilder('u')
            ->innerJoin('u.exams', 'e')
            ->andWhere('e.code like :code')
            ->setParameter('code', $code);
    }


    
    // public function getAllFiltered() {
    //     return $this->filter($this->createQueryBuilder('u')
    //         ->orderBy('u.creation_date', 'desc'));
    // }

    // public function filter($builder) {
    //     return $builder ->andWhere('u.countQ > 0')
    //                 ->andWhere('u.IsLocked = 0')
    //                 ->getQuery()
    //                 ->getResult();
    // }
    



    // /**
    //  * @return eProvider[] Returns an array of eProvider objects
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
    public function findOneBySomeField($value): ?eProvider
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
