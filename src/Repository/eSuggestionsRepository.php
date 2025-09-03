<?php

namespace App\Repository;

use App\Entity\ESuggestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ESuggestion>
 *
 * @method ESuggestion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ESuggestion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ESuggestion[]    findAll()
 * @method ESuggestion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class eSuggestionsRepository extends ServiceEntityRepository
{

    private const DAYS_BEFORE_DUE_REMOVAL = 3;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ESuggestion::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(ESuggestion $entity, bool $flush = true): void
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
    public function remove(ESuggestion $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function findAll()
    {
        return $this->createQueryBuilder('s')
        ->where("s.status IS NULL")
        ->orderBy('s.createdAt', 'DESC')
        ->getQuery()
        ->execute();
    }

    public function countRows()
    {
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->where("s.status IS NULL")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function DueQueryBuilder() {
        return $this->createQueryBuilder('s')
            ->andWhere("DATE_DIFF(CURRENT_DATE(), s.decidedAt) >= :days_old_removal")
            ->setParameters([
                'days_old_removal' => self::DAYS_BEFORE_DUE_REMOVAL
            ]);
    }

    public function countDue()
    {
        return $this->DueQueryBuilder()
            ->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteDue()
    {
        return $this->DueQueryBuilder()
            ->delete()
            ->getQuery()
            ->execute();
    }
    // /**
    //  * @return ESuggestion[] Returns an array of ESuggestion objects
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
    public function findOneBySomeField($value): ?ESuggestion
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
