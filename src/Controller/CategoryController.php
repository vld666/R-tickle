<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @Route("/category/index", methods={"GET"}, name="app_category_index")
     */
    public function indexAction(): Response
    {
        $categories = $this->em->getRepository(Category::class)->findAll();
        return $this->render('/category/index.html.twig',[
            'categories' => $categories,
        ]);
    }


    /**
     * @Route("/category/create", methods={"GET", "POST"}, name="app_category_create")
     */
    public function createAction(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($category);
            $this->em->flush();

            return $this->redirectToRoute('app_category_index');
        }

        return $this->render('/category/create.html.twig',[
            'categoryForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/category/delete/{id}", methods={"GET", "DELETE"}, name="app_category_delete")
     */
    public function deleteAction(Category $category): Response
    {
        $this->em->remove($category);
        $this->em->flush();

        $this->addFlash('success', 'Category was deleted!');

        return $this->redirectToRoute('app_category_index');
    }
}