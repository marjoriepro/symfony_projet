<?php


namespace App\Controller\admin;


use App\Entity\Licence;
use App\Form\LicenceType;
use App\Repository\LicenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminLicenceController extends AbstractController
{
    /**
     * @Route("/admin/licences/", name="admin_list_licence")
     */
    public function listlicence(LicenceRepository $licenceRepository, Request $request)
    {
        $licences = $licenceRepository->findAll();
        return $this->render('admin/licences.html.twig', ['licences' => $licences]);
    }

    /**
     * @Route("/admin/licence/delete/{id}", name="admin_delete_licence")
     */
    public function deleteLicence($id, LicenceRepository $licenceRepository, EntityManagerInterface $entityManager)
    {
        $licence = $licenceRepository->find($id);
        $entityManager->remove($licence);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre licence est supprimé');

        return $this->redirectToRoute('admin_list_licence');
    }

    /**
     * @Route("/admin/licence/update/{id}", name="admin_update_licence")
     */
    public function categoryUpdate(
        $id,
        LicenceRepository $licenceRepository,
        Request $request,
        EntityManagerInterface $entityManagerInterface
    ) {
        $licence = $licenceRepository->find($id);

        $licenceForm = $this->createForm(LicenceType::class, $licence);

        $licenceForm->handleRequest($request);

        if ($licenceForm->isSubmitted() && $licenceForm->isValid()) {
            $entityManagerInterface->persist($licence);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_list_licence');
        }

        return $this->render('admin/licenceupdate.html.twig', ['licenceForm' => $licenceForm->createView()]);
    }

    
    /**
     * @Route("/admin/licence/add/", name="admin_add_licence")
     */
    public function licenceAdd(
        EntityManagerInterface $entityManagerInterface,
        Request $request,
        SluggerInterface $sluggerInterface
    ) {
        $licence= new licence();

        $licenceForm = $this->createForm(LicenceType::class, $licence);

        $licenceForm->handleRequest($request);

        if ($licenceForm->isSubmitted() && $licenceForm->isValid()) {

            $mediaFile = $licenceForm->get('media')->getData();

            if ($mediaFile) {
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $sluggerInterface->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();

                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $licence->setMedia($newFilename);
            }



            $entityManagerInterface->persist($licence);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_list_licence');
        }

        return $this->render("admin/licenceadd.html.twig", ['licenceForm' => $licenceForm->createView()]);
    }
}
