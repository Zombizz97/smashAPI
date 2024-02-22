<?php

namespace App\Repository;

use App\Entity\ProPlayer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProPlayer>
 *
 * @method ProPlayer|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProPlayer|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProPlayer[]    findAll()
 * @method ProPlayer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProPlayer::class);
    }

//    /**
//     * @return ProPlayer[] Returns an array of ProPlayer objects
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

//    public function findOneBySomeField($value): ?ProPlayer
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
