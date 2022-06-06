<?php

namespace App\Controller\RankThree;

use App\Services\FileUploader;

use App\Entity\Certifications;
use App\Form\CertificationsType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/admin/rankthree/creations")
 * @IsGranted("ROLE_COLLABORATOR")
 */
class CreationsController extends AbstractController
{
    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/", name="collab_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('@certifs_collab/index.html.twig', [
            'certifications' => $this->user->getCreations(),
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
            $certification->setCreatedBy($this->user);
            $entityManager->persist($certification);
            $entityManager->flush();

            return $this->redirectToRoute('collab_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@certifs_collab/new.html.twig', [
            'certification' => $certification,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="certif_list", methods={"GET"})
     */
    public function list(Certifications $certification): Response
    {
        if ($this->user->hasRole("ROLE_ADMIN") !== false || $certification->getCreatedBy() == $this->user) {
            return $this->render('@certifs_collab/list.html.twig', [
                'certification' => $certification,
            ]);
        }
            
        return $this->redirectToRoute('collab_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/edit", name="certif_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Certifications $certification, FileUploader $fileUploader): Response {
        if ($this->user->hasRole("ROLE_ADMIN") !== false || $certification->getCreatedBy() == $this->user) {
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
    
                return $this->redirectToRoute('collab_index', [], Response::HTTP_SEE_OTHER);
            }
    
            return $this->render('@certifs_collab/edit.html.twig', [
                'certification' => $certification,
                'form' => $form->createView(),
            ]);
        }
        
        return $this->redirectToRoute('collab_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/delete", name="certif_delete", methods={"POST"})
     */
    public function delete(Request $request, Certifications $certification): Response {

        if (($this->user->hasRole("ROLE_ADMIN") !== false || $certification->getCreatedBy() == $this->user) && $this->isCsrfTokenValid('delete'.$certification->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($certification);
            $entityManager->flush();
        }

        return $this->redirectToRoute('collab_index', [], Response::HTTP_SEE_OTHER);
    }
}