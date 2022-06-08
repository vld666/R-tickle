<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\FavArticle;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
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

    public function findUserFav($user)
    {
        $qb = $this->createQueryBuilder('fa')
            ->andWhere('fa.user = :user')
            ->setParameter('user', $user)
            ->select('fa')
        ;

        return $qb->getQuery()->getResult();
    }


    public function getNrOfLikes($article)
    {
        $qb = $this->createQueryBuilder('fa')
            ->leftJoin(Article::class, 'a', Join::WITH, 'fa.article = a')
            ->setParameter('id', $article)
            ->andWhere('fa.article = :id')
            ->select('count(fa)')
        ;

        return $qb->getQuery()->getScalarResult();
    }
}
