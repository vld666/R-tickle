<?php

namespace App\Repository;

use App\Entity\FavArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FavArticle>
 *
 * @method FavArticle|null find($id, $lockMode = null, $lockVersion = null)
 * @method FavArticle|null findOneBy(array $criteria, array $orderBy = null)
 * @method FavArticle[]    findAll()
 * @method FavArticle[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FavArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FavArticle::class);
    }

    public function add(FavArticle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FavArticle $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FavArticle[] Returns an array of FavArticle objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FavArticle
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


    public function findUserFav($user)
    {
        $qb = $this->createQueryBuilder('fa')
            ->andWhere('fa.user = :user')
            ->setParameter('user', $user)
            ->select('fa')
        ;

        return $qb->getQuery()->getResult();
    }

}
