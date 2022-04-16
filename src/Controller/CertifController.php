<?php

namespace App\Controller;

use App\Services\FileUploader;

use App\Entity\Certifications;
use App\Form\CertificationsType;
use App\Repository\CertificationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/certifications")
 */
class CertifController extends AbstractController
{
    /**
     * @Route("/", name="certif_index", methods={"GET"})
     */
    public function index(CertificationsRepository $certificationsRepository): Response
    {
        return $this->render('admin/certif/index.html.twig', [
            'certifications' => $certificationsRepository->findAll(),
        ]);
    }
    
    /**
     * @Route("/new", name="certif_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response {
        $certification = new Certifications();
        $dThumbnail = $certification->getThumbnailName();

        $form = $this->createForm(CertificationsType::class, $certification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('thumbnail_path')->getData();

            if ($file != null) {
                $fileName = $fileUploader->upload($file, "thumbnail");
                $certification->setThumbnailPath($fileName);
            } else {
                $certification->setThumbnailPath($dThumbnail);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($certification);
            $entityManager->flush();

            return $this->redirectToRoute('certif_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/certif/new.html.twig', [
            'certification' => $certification,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="certif_list", methods={"GET"})
     */
    public function list(Certifications $certification): Response
    {
        return $this->render('admin/certif/list.html.twig', [
            'certification' => $certification,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="certif_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Certifications $certification, FileUploader $fileUploader): Response {
        $oThumbnail = $certification->getThumbnailName();

        $form = $this->createForm(CertificationsType::class, $certification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('thumbnail_path')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the IMAGE file must be processed only when a file is uploaded
            if ($file != null) {
                $fileName = $fileUploader->upload($file, "thumbnail");
                $certification->setThumbnailPath($fileName);
            } else {
                $certification->setThumbnailPath($oThumbnail);
            }
            
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('certif_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/certif/edit.html.twig', [
            'certification' => $certification,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="certif_delete", methods={"POST"})
     */
    public function delete(Request $request, Certifications $certification): Response
    {
        if ($this->isCsrfTokenValid('delete'.$certification->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($certification);
            $entityManager->flush();
        }

        return $this->redirectToRoute('certif_index', [], Response::HTTP_SEE_OTHER);
    }
}
