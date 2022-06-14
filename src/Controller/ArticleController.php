<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\FavArticle;
use App\Entity\PaidArticles;
use App\Entity\Transactions;
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




//        dd($favArticles);
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
        $userWallet = $user->getUserWallet();
        $articlePrice = $article->getPrice();
        $userCredits = $userWallet->getCredits();
        $publisherWallet = $article->getPublishedBy()->getUserWallet();
        $publisherCredits = $publisherWallet->getCredits();
        $platformFee = $article->getPublishedBy()->getPlatformFee();

        if ($userCredits >= $articlePrice){
            $paidArticle = new PaidArticles();
            $transactionBuy = new Transactions();
            $transactionPublisherFee = new Transactions();
            $transactionPlatformFee = new Transactions();

            $paidArticle->setUser($user)->setArticle($article);
            $this->em->persist($paidArticle);

            $userWallet->setCredits($userCredits - $articlePrice);
            $publisherWallet->setCredits($publisherCredits + ($articlePrice * $platformFee));
            $this->em->persist($publisherWallet);

            $transactionBuy
                ->setWallet($userWallet)
                ->setAmount(-$articlePrice)
                ->setType("buyArticle");
            $this->em->persist($transactionBuy);

            $transactionPublisherFee
                ->setWallet($publisherWallet)
                ->setAmount($articlePrice - ($articlePrice * $platformFee))
                ->setType("publisherFee");
            $this->em->persist($transactionPublisherFee);

            $transactionPlatformFee
                ->setAmount($articlePrice * $platformFee)
                ->setType("platformFee");
            $this->em->persist($transactionPlatformFee);

            $this->em->flush();
        }
        return $this->redirectToRoute('app_article_view', ['id' => $article->getId()]);
    }

    /**
     * @Route("/article/create", methods={"GET", "POST"}, name="app_article_create")
     */
    public function createAction(Request $request, SluggerInterface $slugger): Response
    {
        /** @var $user User */
        $user = $this->getUser();
        $article = new Article();
        $paidArticle = new PaidArticles();
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();
            $image = $form->get('image')->getData();
            $id = $article->getId();
            $publisher = $this->getUser();

            $paidArticle->setUser($user)->setArticle($article);
            $this->em->persist($paidArticle);

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
            $article->setViews(0);

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
        $favArticles = $this->em->getRepository(FavArticle::class)->findAll();
        $views = $article->getViews();
        $views = $views + 1;
        $this->em->persist($article->setViews($views));
        $this->em->flush();

        return $this->render('/article/view.html.twig', [
            'nrFav' => $nrFav,
            'favArticles' => $favArticles,
            'article' => $article,
            'userPaidArticles' => $userPaidArticles,
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

        return $this->redirectToRoute('app_article_view', ['id' => $article->getId()]);
    }


    /**
     * @Route("/article/delete/{id}", methods={"GET", "DELETE"}, name="app_article_delete")
     */
    public function deleteAction(Article $article): Response
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
