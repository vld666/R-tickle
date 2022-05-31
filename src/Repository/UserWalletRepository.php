<?php

namespace App\Repository;

use App\Entity\UserWallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<UserWallet>
 *
 * @method UserWallet|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserWallet|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserWallet[]    findAll()
 * @method UserWallet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserWalletRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWallet::class);
    }

    public function add(UserWallet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UserWallet $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return UserWallet[] Returns an array of UserWallet objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?UserWallet
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function getTotalFees(): int
    {
        $qb = $this->createQueryBuilder('f')
            ->select('f.credits')
            ->where('f.id = :id')
            ->setParameter('id', 1);

        return $qb->getQuery()->getSingleScalarResult();

    }

//
//$qb = $this->createQueryBuilder('u')
//->leftJoin(PaidArticles::class, 'pa', Join::WITH, 'pa.user = u')
//->where('u.id = :id')
//->setParameter('id', $user)
//->select('IDENTITY(pa.article)')
//;
//
//return $qb->getQuery()->getResult();
}
