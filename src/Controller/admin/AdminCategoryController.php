<?php


namespace App\Controller\admin;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminCategoryController extends AbstractController
{
    /**
     * @Route("/admin/categories/", name="admin_list_category")
     */
    public function listCategory(CategoryRepository $categoryRepository, Request $request)
    {
        $categories = $categoryRepository->findAll();
        return $this->render('admin/categories.html.twig', ['categories' => $categories]);
    }

    /**
     * @Route("/admin/category/delete/{id}", name="admin_delete_category")
     */
    public function deletecategory($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManager)
    {
        $category = $categoryRepository->find($id);
        $entityManager->remove($category);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre catégorie est supprimé'
        );

        return $this->redirectToRoute('admin_list_category');
    }

    /**
     * @Route("/admin/category/update/{id}", name="admin_update_category")
     */
    public function categoryUpdate(
        $id,
        CategoryRepository $categoryRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ) {
        $category = $categoryRepository->find($id);

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_list_category');
        }

        return $this->render('admin/categoryupdate.html.twig', ['categoryForm' => $categoryForm->createView()]);
    }




    /**
     * @Route("/admin/category/add/", name="admin_add_category")
     */
    public function categoryAdd(
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        SluggerInterface $sluggerInterface
    ) {
        $category = new Category();

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {

            $mediaFile = $categoryForm->get('media')->getData();

            if ($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $sluggerInterface->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $category->setMedia($newFilename);
            }



            $entityManagerInterface->persist($category);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_list_category');
        }

        return $this->render("admin/categoryadd.html.twig", ['categoryForm' => $categoryForm->createView()]);
    }
}
