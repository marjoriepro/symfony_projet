<?php


namespace App\Controller\front;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/front/categories/", name="list_category")
     */
    public function listCategory(CategoryRepository $categoryRepository, Request $request)
    {
        $categories = $categoryRepository->findAll();
        return $this->render('front/categories.html.twig', ['categories' => $categories]);
    }

    /**
     * @Route("front/category/{id}", name="show_category")
     */
    public function showCategory(CategoryRepository $categoryRepository, $id)
    {
        $category = $categoryRepository->find($id);

        return $this->render("front/category.html.twig", ['category' => $category]);
    }



}




