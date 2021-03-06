<?php

namespace App\Repository;

use App\Entity\PaidArticles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PaidArticles>
 *
 * @method PaidArticles|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaidArticles|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaidArticles[]    findAll()
 * @method PaidArticles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaidArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaidArticles::class);
    }

    public function add(PaidArticles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PaidArticles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
