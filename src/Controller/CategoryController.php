<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
//use ContainerULMl0J6\getMessenger_Listener_StopWorkerOnSigtermSignalListenerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends ApiController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    /**
     * @Route("/category/index", methods={"GET", "POST"}, name="app_category_index")
     */
    public function indexAction(Request $request): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($category);
            $this->em->flush();

            return $this->redirectToRoute('app_category_index');
        }


        $categories = $this->em->getRepository(Category::class)->findAll();
        return $this->render('/category/index.html.twig',[
            'categories' => $categories,
            'categoryForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/category/delete/{id}", methods={"GET", "DELETE"}, name="app_category_delete")
     */
    public function deleteAction(Category $category): Response
    {
        $this->em->remove($category);
        $this->em->flush();

        return $this->redirectToRoute('app_category_index');
    }

    /**
     * @Route("/category/edit/{id}", methods={"GET", "POST"}, name="app_category_edit")
     */
    public function editAction(Category $category, Request $request): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->em->persist($category);
            $this->em->flush();

            $this->addFlash('success', 'Category updated!');
        }

        return $this->render('/category/edit.html.twig', [
            'categoryForm' => $form->createView(),
        ]);
    }




    // ------------- API ----------------

    /**
     * @Route("/api/category/index", methods={"GET"})
     */
    public function apiShowCategories(): Response
    {
        $categories = $this->em->getRepository(Category::class)->findAll();
        $arrayCollection = array();

        foreach($categories as $category){
            $arrayCollection[] = array(
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'color' => $category->getColor()
            );
        }

        $response = new JsonResponse($arrayCollection);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);

        return $response;
    }


    /**
     * @Route("/api/category/create", methods={"GET", "POST"})
     */
    public function apiCreateCategory(Request $request): Response
    {
        $form = $this->createForm(CategoryFormType::class);
        $form->submit($request->request->all());

        if(!$form->isSubmitted() || !$form->isValid()){
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }
        /**  @var Category $category */
        $category = $form->getData();
        $this->em->persist($category);
        $this->em->flush();

        return $this->respond($category);
    }


    /**
     * @Route("/api/category/edit/{id}", methods={"GET", "POST"})
     */
    public function apiEditCategory(Category $category, Request $request): Response
    {
        $category = $this->em->getRepository(Category::class)->find($category);

        $form = $this->createForm(CategoryFormType::class, $category);
        $form->submit($request->request->all());

        if(!$form->isSubmitted() || !$form->isValid()){
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Category $category */
        $category = $form->getData();
        $this->em->persist($category);
        $this->em->flush();

        return $this->respond('Category updated!');

    }

    /**
     * @Route("/api/category/show/{id}", methods={"GET"})
     */
    public function apiShowCategory(Category $category): Response
    {
        $category = $this->em->getRepository(Category::class)->showCategory($category);
        $response = new JsonResponse($category);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT);

        return $this->respond($category);
    }

    /**
     * @Route("/api/category/delete/{id}", methods={"DELETE"}, name="app_api_category_delete")
     */
    public function apiDeleteCategory(Category $category): Response
    {
        $this->em->remove($category);
        $this->em->flush();

        return $this->respond('Category deleted!');
    }

}