<?php

namespace App\Repository;

use App\Entity\Payload;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Payload>
 *
 * @method Payload|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payload|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payload[]    findAll()
 * @method Payload[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayloadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Payload::class);
    }

//    /**
//     * @return Payload[] Returns an array of Payload objects
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

//    public function findOneBySomeField($value): ?Payload
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
