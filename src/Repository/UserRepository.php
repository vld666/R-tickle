<?php

namespace App\Repository;

use App\Entity\Article;
use App\Entity\FavArticle;
use App\Entity\PaidArticles;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function findUserArticles($user)
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin(Article::class, 'a', Join::WITH, 'a.publishedBy = u')
            ->where('u.id = :id')
            ->setParameter('id', $user)
            ->select('a')
        ;

        return $qb->getQuery()->getResult();
    }

    public function findFavArticles($user)
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin(FavArticle::class,'fa', Join::WITH, 'fa.user = u')
            ->leftJoin(Article::class, 'a', Join::WITH, 'fa.article = a')
            ->where('u.id = :id')
            ->setParameter('id', $user)
            ->select('a')
        ;

        return $qb->getQuery()->getResult();

    }

    public function getUserPaidArticles($user)
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin(PaidArticles::class, 'pa', Join::WITH, 'pa.user = u')
            ->leftJoin(Article::class, 'a', Join::WITH, 'pa.article = a')
            ->where('u.id = :id')
            ->setParameter('id', $user)
//            ->select('IDENTITY(pa.article)')
            ->select('pa')
            ;

        return $qb->getQuery()->getResult();
    }

    public function remainingCredits($article, $user)
    {
        $articlePrice = $article->getPrice();
        $creditsAvailable = $user->getUserWallet()->getCredits();

        return $creditsAvailable - $articlePrice;
    }

//    public function findUserOwnedArticles($user)
//    {
//        $qb = $this->createQueryBuilder('u')
//
//
//            ;
//
//        return $qb->getQuery()->getResult();
//    }
}
