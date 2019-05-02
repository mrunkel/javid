<?php

namespace App\Repository;

use App\Entity\Resolutions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Resolutions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Resolutions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Resolutions[]    findAll()
 * @method Resolutions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResolutionsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Resolutions::class);
    }

    // /**
    //  * @return Resolutions[] Returns an array of Resolutions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Resolutions
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
