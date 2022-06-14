<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\FavArticle;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
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
    public function addFav(Article $article, \Symfony\Component\HttpFoundation\Request $request): Response
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

        $route = $request->headers->get('referer');  //get last route


        return $this->redirect($route); //redirect to last route


    }
}