<?php

namespace App\Controller\front;

use App\Repository\CategoryRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/front/home/", name="front_home")
     */
    public function home(CategoryRepository $categoryRepository)
    {
        $id = rand(1, 10);
        $category = $categoryRepository->find($id);
        if ($category) {
            return $this->render('front/home.html.twig', ['category' => $category]);
        } else {
            return $this->redirectToRoute('front_home');
        }
    }
}