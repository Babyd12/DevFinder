<?php

namespace App\Repository;

use App\Entity\DescriptionCompetence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DescriptionCompetence>
 *
 * @method DescriptionCompetence|null find($id, $lockMode = null, $lockVersion = null)
 * @method DescriptionCompetence|null findOneBy(array $criteria, array $orderBy = null)
 * @method DescriptionCompetence[]    findAll()
 * @method DescriptionCompetence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DescriptionCompetenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DescriptionCompetence::class);
    }

//    /**
//     * @return DescriptionCompetence[] Returns an array of DescriptionCompetence objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DescriptionCompetence
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
