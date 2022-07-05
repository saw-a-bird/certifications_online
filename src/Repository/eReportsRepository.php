<?php

namespace App\Repository;

use App\Entity\eReport;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<eReport>
 *
 * @method eReport|null find($id, $lockMode = null, $lockVersion = null)
 * @method eReport|null findOneBy(array $criteria, array $orderBy = null)
 * @method eReport[]    findAll()
 * @method eReport[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class eReportsRepository extends ServiceEntityRepository
{

    private const DAYS_BEFORE_SPAM_REMOVAL = 2;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, eReport::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(eReport $entity, bool $flush = true): void
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
    public function remove(eReport $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function countRows()
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where("r.status = '-'")
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function SpamQueryBuilder() {
        return $this->createQueryBuilder('r')
            ->where("r.status = :state_spam")
            ->andWhere("DATE_DIFF(CURRENT_DATE(), r.createdAt) >= :days_spam_removal")
            ->setParameters([
                'state_spam' => 'Spam',
                'days_spam_removal' => self::DAYS_BEFORE_SPAM_REMOVAL
            ]);
    }

    public function countSpam()
    {
        return $this->SpamQueryBuilder()
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteSpam()
    {
        return $this->SpamQueryBuilder()
            ->delete()
            ->getQuery()
            ->execute();
    }


    // /**
    //  * @return eReport[] Returns an array of eReport objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?eReport
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
