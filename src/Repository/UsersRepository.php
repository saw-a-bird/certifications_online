<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

class UsersRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function loadUserByIdentifier($email) : ?UserInterface
    {
        return $this->createQueryBuilder('u')
        ->where('u.email = :email')
        ->setParameter('email', $email)
        ->getQuery()
        ->getOneOrNullResult();
    }


    public function loadUserByUsername($username) : ?UserInterface
    {
        return $this->createQueryBuilder('u')
        ->where('u.username = :username')
        ->setParameter('username', $username)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function loadUserByID($id)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findAllExcept($id)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id != :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }
    
}
