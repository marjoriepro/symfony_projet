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


    /**
     * @Route("front/licence/{id}", name="show_licence")
     */
    public function showLicence(LicenceRepository $licenceRepository, $id)
    {
        $licence = $licenceRepository->find($id);

        return $this->render("front/licence.html.twig", ['licence' => $licence]);
    }

}