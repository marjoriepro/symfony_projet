<?php


namespace App\Controller\admin;


use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminMediaController extends AbstractController
{
    /**
     * @Route("/admin/medias/", name="admin_list_media")
     */
    public function listMedias(MediaRepository $mediaRepository){
        $medias = $mediaRepository->findAll();
        return $this->render('admin/medias.html.twig',['medias' => $medias]);
    }

    /**
     * @Route("/admin/media/delete/{id}", name="admin_delete_media")
     */
    public function deleteMedia($id, MediaRepository $mediaRepository, EntityManagerInterface $entityManager)
    {
        $media = $mediaRepository->find($id);
        $entityManager->remove($media);
        $entityManager->flush();
        $this->addFlash(
            'notice',
            'Votre image est supprimé');

        return $this->redirectToRoute('admin_list_media');
    }

    /**
     * @Route("/admin/media/update/{id}", name="admin_update_media")
     */
    public function updateMedia($id, MediaRepository $mediaRepository, Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface)
    {
        $media = $mediaRepository->find($id);
        $mediaForm = $this->createForm(MediaType::class, $media);
        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {
            $mediaFile = $mediaForm->get('src')->getData();
            if ($mediaFile) {
                // On créé un nom unique avec le nom original de l'image pour éviter
                // tout problème
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                // on utilise slug sur le nom original de l'image pour avoir un nom valide
                $safeFilename = $sluggerInterface->slug($originalFilename);
                // on ajoute un id unique au nom de l'image
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();


                // On déplace le fichier dans le dossier public/media
                // la destination du fichier est enregistré dans 'images_directory'
                // qui est défini dans le fichier config\services.yaml
                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $media->setSrc($newFilename);
            }

            $media->setAlt($mediaForm->get('title')->getData());

            $entityManagerInterface->persist($media);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Votre image est modifiée');

            return $this->redirectToRoute("admin_list_media");

        }
        return $this->render('admin/mediaadd.html.twig', [
            'mediaForm' => $mediaForm->createView()]);
    }

    /**
     * @Route("admin/media/add", name="admin_media_add")
     */
    public function createMedia(
        Request $request,
        EntityManagerInterface $entityManagerInterface,
        SluggerInterface $sluggerInterface
    ) {

        $media = new Media();

        $mediaForm = $this->createForm(MediaType::class, $media);

        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {

            $mediaFile = $mediaForm->get('src')->getData();

            if ($mediaFile) {
                // On créé un nom unique avec le nom original de l'image pour éviter
                // tout problème
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                // on utilise slug sur le nom original de l'image pour avoir un nom valide
                $safeFilename = $sluggerInterface->slug($originalFilename);
                // on ajoute un id unique au nom de l'image
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $mediaFile->guessExtension();


                // On déplace le fichier dans le dossier public/media
                // la destination du fichier est enregistré dans 'images_directory'
                // qui est défini dans le fichier config\services.yaml
                $mediaFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $media->setSrc($newFilename);
            }

            $media->setAlt($mediaForm->get('title')->getData());

            $entityManagerInterface->persist($media);
            $entityManagerInterface->flush();

            $this->addFlash(
                'notice',
                'Votre image est ajoutée');

            return $this->redirectToRoute("admin_list_media");
        }

        return $this->render('admin/mediaadd.html.twig', ['mediaForm' => $mediaForm->createView()]);
    }

}