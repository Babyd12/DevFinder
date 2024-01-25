<?php

namespace App\Repository;

use App\Entity\ProjetStatu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjetStatu>
 *
 * @method ProjetStatu|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjetStatu|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjetStatu[]    findAll()
 * @method ProjetStatu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjetStatuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjetStatu::class);
    }

//    /**
//     * @return ProjetStatu[] Returns an array of ProjetStatu objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ProjetStatu
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
