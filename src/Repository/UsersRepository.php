<?php

namespace App\Repository;
use App\Entity\Client;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Users|null find($id, $lockMode = null, $lockVersion = null)
 * @method Users|null findOneBy(array $criteria, array $orderBy = null)
 * @method Users[]    findAll()
 * @method Users[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    // /**
    //  * @return Users[] Returns an array of Users objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findOneByEmail($email): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function getAllUsers()
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.role != :role')
            ->setParameter('role', 'admin')
            ->getQuery()
            ->getResult();
    }

    public function findClientByUserIdPopulated()
    {
        return $this->createQueryBuilder('u')
            ->join('u.id','c.userId')
            ->getQuery()
            ->getResult();
    }

    public function disableAccount($userId,$isDisabled)
    {
        try {
            $update = $this->createQueryBuilder('u')
                ->update(Users::class, 'u')
                ->set('u.isDisabled',':isDisabled')
                ->where('u.id = :id')
                ->setParameter('isDisabled', $isDisabled)
                ->setParameter('id', $userId)
                ->getQuery()
            ;
            dump($update);
            return $update->execute();
        } catch (\Exception $e) {
            return 0;
        }
    }
}
