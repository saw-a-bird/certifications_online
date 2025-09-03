<?php

namespace App\Repository;

use App\Entity\History;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<History>
 *
 * @method History|null find($id, $lockMode = null, $lockVersion = null)
 * @method History|null findOneBy(array $criteria, array $orderBy = null)
 * @method History[]    findAll()
 * @method History[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoryRepository extends ServiceEntityRepository
{
    
    private const DAYS_BEFORE_SPAM_REMOVAL = 30;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(History $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(History $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function OldQueryBuilder() {
        return $this->createQueryBuilder('h')
            ->andWhere("DATE_DIFF(CURRENT_DATE(), h.createdAt) >= :days_old_removal")
            ->setParameters([
                'days_old_removal' => self::DAYS_BEFORE_SPAM_REMOVAL
            ]);
    }

    public function countOld()
    {
        return $this->OldQueryBuilder()
            ->select('COUNT(h.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteOld()
    {
        return $this->OldQueryBuilder()
            ->delete()
            ->getQuery()
            ->execute();
    }

    public function findAll()
    {
        return $this->createQueryBuilder('h')
        ->orderBy('h.createdAt', 'DESC')
        ->getQuery()
        ->execute();
    }

    // /**
    //  * @return History[] Returns an array of History objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?History
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
