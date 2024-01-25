<?php

namespace App\Repository;

use App\Entity\LangageDeProgrammation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LangageDeProgrammation>
 *
 * @method LangageDeProgrammation|null find($id, $lockMode = null, $lockVersion = null)
 * @method LangageDeProgrammation|null findOneBy(array $criteria, array $orderBy = null)
 * @method LangageDeProgrammation[]    findAll()
 * @method LangageDeProgrammation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LangageDeProgrammationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LangageDeProgrammation::class);
    }

//    /**
//     * @return LangageDeProgrammation[] Returns an array of LangageDeProgrammation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LangageDeProgrammation
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
