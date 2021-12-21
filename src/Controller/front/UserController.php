<?php

namespace App\Controller\front;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{

    /**
     * @Route("/user/new", name="new_user")
     */
    public function addUser(
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface
    ) {
        $user = new User();

        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user->setRoles(["ROLE_USER"]);
            $user->setDate(new \DateTime("NOW"));

            $plainPassword = $userForm->get('password')->getData();
            $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("app_login");
        }

        return $this->render("front/userform.html.twig", ['userForm' => $userForm->createView()]);
    }

    /**
     * @Route("/user/update/{id}", name="update_user")
     */

    public function userUpdate(
        $id,
        UserRepository $userRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        UserPasswordHasherInterface $userPasswordHasherInterface
    ) {

        $user = $userRepository->find($id);

        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {

            $plainPassword = $userForm->get('password')->getData();
            $hashedPassword = $userPasswordHasherInterface->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);

            $entityManagerInterface->persist($user);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("app_login");
        }

        return $this->render("front/userform.html.twig", ['userForm' => $userForm->createView()]);
    }
}