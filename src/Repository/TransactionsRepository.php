<?php

namespace App\Repository;

use App\Entity\Transactions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transactions>
 *
 * @method Transactions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transactions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transactions[]    findAll()
 * @method Transactions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transactions::class);
    }

    public function add(Transactions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Transactions $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return transactions[] Returns an array of transactions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?transactions
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getTotalSales(){
        $qb = $this->createQueryBuilder('t')
            ->where('t.type = :type')
            ->setParameter('type', 'platformFee')
            ->select('sum(t.amount)')
            ;
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function showTransaction(Transactions $transaction)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->andWhere('t.id = :id')
            ->setParameter('id', $transaction);

        return $qb->getQuery()->getArrayResult();
    }


    public function getUserTransactions($user)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('t')
            ->where('t.wallet = :userWallet')
            ->setParameter('userWallet', $user->getUserWallet()->getId())
            ->orderBy('t.createdAt', 'desc')
        ;
        return $qb->getQuery()->getResult();
    }
}
