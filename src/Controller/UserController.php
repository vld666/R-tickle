<?php

namespace App\Controller;

use App\Entity\FavArticle;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @Route("/user/profile", name="app_user_profile")
     */
    public function profile(Request $request, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

//        $pass = $user->getPassword();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();
        }


        $userArticles = $this->em->getRepository(User::class)->findUserArticles($user);

        $userFavArticles = $this->em->getRepository(User::class)->findFavArticles($user);



        return $this->render('user/profile.html.twig', [
            'user' => $user,
            'userFavArticles' => $userFavArticles,
            'userArticles' => $userArticles,
            'userForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/admin/user/index", methods={"GET"}, name="app_user_index")
     */
    public function index(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig',[
            'users' => $users
        ]);
    }

    /**
     * @Route("/user/credits", methods={"GET", "POST"}, name="app_user_credits")
     */
    public function credits(Request $request): Response
    {


        return $this->render('/user/credits.html.twig',[
        ]);
    }

//
//    /**
//     * @Route("/user/credits/add100", name="app_user_credits_add100"}
//     */
//    public function add100(): Response
//    {
////        $user = $this->getUser();
////        $userC = $user->getCredits();
////
////        $userNewC = $userC + 100;
////
////        $user->setCredits($userNewC);
////        $this->em->persist($user);
////        $this->em->flush();
//
//        return $this->redirectToRoute('app_user_credits');
//
//    }

    /**
     * @Route("/user/credits/add/{amount}", methods={"GET", "POST"}, name="app_user_creditsAdd")
     */
    public function addCredits($amount): Response
    {
            $user = $this->getUser();
            $userC = $user->getCredits();
            $userNewC = $userC + $amount;
            $user->setCredits($userNewC);
            $this->em->persist($user);
            $this->em->flush();

        return $this->redirectToRoute('app_user_credits');
    }





}