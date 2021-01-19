<?php

namespace App\Repository;

use App\Entity\PaymentDetails;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaymentDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentDetails[]    findAll()
 * @method PaymentDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentDetailsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaymentDetails::class);
    }

    // /**
    //  * @return PaymentDetails[] Returns an array of PaymentDetails objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaymentDetails
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
