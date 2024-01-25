<?php

namespace App\Repository;

use App\Entity\Immersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Immersion>
 *
 * @method Immersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Immersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Immersion[]    findAll()
 * @method Immersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImmersionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Immersion::class);
    }

//    /**
//     * @return Immersion[] Returns an array of Immersion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Immersion
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
