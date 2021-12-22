<?php


namespace App\Controller\front;

use App\Entity\Comment;
use App\Entity\Like;
use App\Form\CommentType;
use App\Repository\LikeRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/front/products" , name="list_product")
     */
    public function listProduct(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();
        return $this->render("front/products.html.twig", ['products' => $products]);
    }


    /**
     * @Route("front/product/{id}", name="show_product")
     */
    public function showProduct(
        ProductRepository $productRepository,
        $id,
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        UserRepository $userRepository
    ) {
        $product = $productRepository->find($id);

        $comment = new Comment();

        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);


        if ($commentForm->isSubmitted() && $commentForm->isValid()) {

            $user = $this->getUser();

            if ($user) {
                $user_mail = $user->getUserIdentifier();
                $user_true = $userRepository->findOneBy(['email' => $user_mail]);
            }


            $comment->setDate(new \DateTime("NOW"));
            $comment->setProduct($product);
            $comment->setUser($user_true);

            $entityManagerInterface->persist($comment);
            $entityManagerInterface->flush();
        }

        return $this->render("front/product.html.twig", [
            'product' => $product,
            'commentForm' => $commentForm->createView()
        ]);
    }


    /**
     * @Route("/front/search/", name="front_search")
     */
    public function frontSearch(ProductRepository $productRepository, Request $request)
    {
        // Récupérer les données rentrées dans le formulaire
        $term = $request->query->get('term');

        $products = $productRepository->searchByTerm($term);

        return $this->render('front/search.html.twig', ['products' => $products]);
    }


     /**
     * @Route("/front/like/product/{id}", name="product_like")
     */
    public function likeProduct(
        $id,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManagerInterface,
        LikeRepository $likeRepository
    ) {
        $product = $productRepository->find($id);
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'code' => 403,
                'message' => "Vous devez être connecté"
            ], 403);
        }

        if ($product->isLikedByUser($user)) {
            $like = $likeRepository->findOneBy([
                'product' => $product,
                'user' => $user
            ]);

            $entityManagerInterface->remove($like);
            $entityManagerInterface->flush();

            return $this->json([
                'code' => 200,
                'message' => "Le like a été supprimé",
                'likes' => $likeRepository->count(['product' => $product])
            ], 200);
        }

        $like = new Like();
        $like->setProduct($product);
        $like->setUser($user);

        $entityManagerInterface->persist($like);
        $entityManagerInterface->flush();

        return $this->json([
            'code' => 200,
            'message' => "Le like a été enregistré",
            'likes' => $likeRepository->count(['product' => $product])
        ], 200);
    }

}

