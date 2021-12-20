<?php


namespace App\Controller\front;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

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


    

}




