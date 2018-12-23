<?php

namespace App\Repository;

use App\Entity\ShoppingBag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ShoppingBag|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShoppingBag|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShoppingBag[]    findAll()
 * @method ShoppingBag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShoppingBagRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ShoppingBag::class);
    }

    // /**
    //  * @return ShoppingBag[] Returns an array of ShoppingBag objects
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
    public function findOneBySomeField($value): ?ShoppingBag
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
