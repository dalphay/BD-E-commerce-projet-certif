<?php

namespace App\Repository;

use App\Entity\ToBuy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ToBuy|null find($id, $lockMode = null, $lockVersion = null)
 * @method ToBuy|null findOneBy(array $criteria, array $orderBy = null)
 * @method ToBuy[]    findAll()
 * @method ToBuy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ToBuyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ToBuy::class);
    }

//    /**
//     * @return ToBuy[] Returns an array of ToBuy objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ToBuy
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
