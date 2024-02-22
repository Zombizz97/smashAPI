<?php

namespace App\Repository;

use App\Entity\PayloadType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PayloadType>
 *
 * @method PayloadType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PayloadType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PayloadType[]    findAll()
 * @method PayloadType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PayloadTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PayloadType::class);
    }

//    /**
//     * @return PayloadType[] Returns an array of PayloadType objects
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

//    public function findOneBySomeField($value): ?PayloadType
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
