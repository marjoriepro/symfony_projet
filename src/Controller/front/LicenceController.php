<?php


namespace App\Controller\front;


use App\Entity\Licence;
use App\Form\LicenceType;
use App\Repository\LicenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class LicenceController extends AbstractController
{
    /**
     * @Route("/front/licences/", name="list_licence")
     */
    public function listlicence(LicenceRepository $licenceRepository, Request $request)
    {
        $licences = $licenceRepository->findAll();
        return $this->render('front/licences.html.twig', ['licences' => $licences]);
    }

}