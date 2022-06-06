<?php

namespace App\Controller;

use App\Entity\FavArticle;
use App\Entity\Transactions;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends ApiController
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
     * @Route("/admin/user/edit/{id}", methods={"GET", "POST"}, name="app_user_edit")
     */
    public function editAction(Request $request, User $user): Response
    {
        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Category updated!');
        }

        return $this->render('user/edit.html.twig',[
            'userForm' => $form->createView()
        ]);
    }


    /**
     * @Route("/user/credits", methods={"GET", "POST"}, name="app_user_credits")
     */
    public function credits(): Response
    {
        return $this->render('/user/credits.html.twig',[
        ]);
    }



    /**
     * @Route("/user/credits/add/{amount}", methods={"GET", "POST"}, name="app_user_creditsAdd")
     */
    public function addCredits($amount): Response
    {
        $user = $this->getUser();
        $userWallet = $user->getUserWallet();
        $userC = $userWallet->getCredits();

        $userNewC = $userC + $amount;
        $userWallet->setCredits($userNewC);

        $transaction = new Transactions();
        $transaction->setWallet($userWallet)->setAmount($amount)->setType("buyCredits");

        $this->em->persist($userWallet);
        $this->em->persist($transaction);
        $this->em->flush();

        return $this->redirectToRoute('app_user_credits');
    }


    /**
     * @Route("/api/user/index", methods={"GET"}, name="app_api_user_index")
     */
    public function apiUserList(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();
        $arrayCollection = array();

        foreach($users as $user){
            $arrayCollection[] = array(
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'mail' => $user->getMail(),
                'phone' => $user->getPhone(),
                'fullName' => $user->getFullName(),
                'platformFee' => $user->getPlatformFee(),
                'createdAt' => $user->getCreatedAt(),
            );
        }

        $response = new JsonResponse($arrayCollection);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);

        return $response;
    }


    /**
     * @Route("/admin/api/user/delete/{id}", methods={"DELETE"}, name="app_api_user_delete")
     */
    public function apiDeleteAction(User $user): JsonResponse
    {
        $this->em->remove($user);
        $this->em->flush();

        return new JsonResponse('User deleted!');
    }



}