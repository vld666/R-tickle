<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\FavArticle;
use App\Entity\PaidArticles;
use App\Entity\User;
use App\Form\ArticleFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ArticleController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @Route("/article/index", name="app_article_index")
     */
    public function indexAction(): Response
    {

        $articles = $this->em->getRepository(Article::class)->findBy(['visible' => 1]);

        $userPaidArticles = $this->em->getRepository(User::class)->getUserPaidArticles($this->getUser());

        $favArticles = $this->em->getRepository(FavArticle::class)->findBy(['user' => $this->getUser()]);

//        dd($userPaidArticles);

        return $this->render('article/index.html.twig', [
            'articles' => $articles,
            'favArticles' => $favArticles,
            'userPaidArticles' => $userPaidArticles
        ]);
    }

    /**
     * @Route("/article/buy/{id}", methods={"GET"}, name="app_article_buy")
     */
    public function buyArticle(Article $article): Response
    {
        $remainingCredits = $this->em->getRepository(User::class)->remainingCredits($article, $this->getUser());




        return $this->render('article/buy.html.twig',[
            'remainingCredits' => $remainingCredits,
            'article' => $article
        ]);
    }

    /**
     * @Route("/article/buy/{id}/ok", methods={"POST", "GET"}, name="app_article_buy_ok")
     */
    public function buyArticleOk(Article $article)
    {
        /** @var $user User */
        $user = $this->getUser();
        $articlePrice = $article->getPrice();
        $userCredits = $user->getCredits();

        if ($userCredits >= $articlePrice){
            $paidArticle = new PaidArticles();

            $paidArticle->setUser($user)->setArticle($article);
            $this->em->persist($paidArticle);

            $user->setCredits($userCredits - $articlePrice);

            $this->em->flush();
        }else{
            return $this->redirectToRoute('app_user_profile');
        }


        return $this->redirectToRoute('app_article_view', ['id' => $article->getId()]);

    }


    /**
     * @Route("/article/create", methods={"GET", "POST"}, name="app_article_create")
     */
    public function createAction(Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $image = $form->get('image')->getData();
            $id = $article->getId();
            $publisher = $this->getUser();

            if($image){
                $originalFileName = $image->getClientOriginalName();
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $id . $safeFileName . '-' . uniqid() . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('photos_directory'),
                    $newFileName
                );

                $article->setImage($newFileName);
            }
            $article->setPublishedBy($publisher);
            $article->setVisible(1);

            $this->em->persist($article);
            $this->em->flush();

            return $this->redirectToRoute('app_article_index');
        }


        return $this->render('/article/create.html.twig', [
            'articleForm' => $form->createView(),
            'article' => $article,
        ]);
    }


    /**
     * @Route("/article/view/{id}", methods={"GET"}, name="app_article_view")
     */
    public function viewAction(Article $article): Response
    {
        $article = $this->em->getRepository(Article::class)->find($article);

        $nrFav = $this->em->getRepository(FavArticle::class)->getNrOfLikes($article);

        $userPaidArticles = $this->em->getRepository(User::class)->getUserPaidArticles($this->getUser());

        return $this->render('/article/view.html.twig', [
            'nrFav' => $nrFav,
            'article' => $article,
            'userPaidArticles' => $userPaidArticles
        ]);
    }

    /**
     * @Route("/article/hide/{id}", methods={"GET", "POST"}, name="app_article_hide")
     */
    public function hideArticleAction(Article $article): Response
    {
        $v = $article->getVisible();

        if ($v == 1){
            $article->setVisible(0);
        }else {
            $article->setVisible(1);
        }
        $this->em->flush();

//        return $this->redirectToRoute('app_article_index');
        return $this->redirectToRoute('app_article_view', ['id' => $article->getId()]);

    }


    /**
     * @Route("/article/delete/{id}", methods={"GET", "DELETE"}, name="app_article_delete")
     */
    public function deleteAction(Article $article)
    {
        $this->em->remove($article);
        $this->em->flush();

        return $this->redirectToRoute('app_article_index');


    }


    /**
     * @Route("/article/edit/{id}", methods={"GET", "POST"}, name="app_article_edit")
     */
    public function editAction(Request $request, Article $article, SluggerInterface $slugger):Response
    {
        $article = $this->em->getRepository(Article::class)->find($article);

        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $image = $form->get('image')->getData();
            $id = $article->getId();

            if($image){
                $originalFileName = $image->getClientOriginalName();
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $id . $safeFileName . '-' . uniqid() . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('photos_directory'),
                    $newFileName
                );



                $article->setImage($newFileName);
            }

            $this->em->persist($article);
            $this->em->flush();

            return $this->redirectToRoute('app_article_view', ['id' => $article->getId()]);
        }

        return $this->render('/article/edit.html.twig',[
            'articleForm' => $form->createView(),
            'article' => $article
        ]);
    }





}
