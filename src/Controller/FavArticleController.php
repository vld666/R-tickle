<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\FavArticle;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FavArticleController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/addtofav/{id}", methods={"GET", "POST"}, name="app_add_fav")
     */
    public function addFav(Article $article): Response
    {
        $favorite = $this->em->getRepository(FavArticle::class)->findOneBy([
            'article' => $article,
            'user' => $this->getUser()
        ]);

        /** @var $user User */
        $user = $this->getUser();

        if(!$favorite) {
            $favorite = new FavArticle();

            $favorite->setArticle($article)
                    ->setUser($user);
            $this->em->persist($favorite);
        }else {
            $this->em->remove($favorite);
        }

        $this->em->flush();

        return $this->redirectToRoute('app_article_index');
    }
}