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
    public function index(Request $request, SluggerInterface $slugger): Response
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

}