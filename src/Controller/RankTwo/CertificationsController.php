<?php

namespace App\Controller\RankTwo;

use App\Services\ImageUploader;

use App\Entity\eProvider;
use App\Entity\Certification;
use App\Entity\Exam;
use App\Entity\History;
use App\Form\CertificationsType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/moderator/ressources/certifications")
 * @IsGranted("ROLE_MODERATOR")
 */
class CertificationsController extends AbstractController
{
    private $entityManager;
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager) {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/new/to/{id}", name="certif_new", methods={"GET","POST"})
     */
    public function new(eProvider $eProvider, Request $request, ImageUploader $imgUploader): Response {
        $certification = new Certification();
        $certification->setEProvider($eProvider);

        $form = $this->createForm(CertificationsType::class, $certification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('thumbnail_path')->getData();

            if ($file != null) {
                $fileName = $imgUploader->upload($file, "thumbnail_certif");
                $certification->setThumbnailPath($fileName);
            }

            $this->entityManager->persist($certification);
            $this->entityManager->persist(new History($this->user, "created new certification#".$certification->getId()." (title: '".$certification->getTitle()."')"));
            $this->entityManager->flush();

            $this->addFlash('success', 'Successfully created a new certification.');

            return $this->redirectToRoute('certif_edit', ["id" => $certification->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@mod_root/certifs/new.html.twig', [
            'certif' => $certification,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="certif_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Certification $certification, ImageUploader $imgUploader): Response {

        $form = $this->createForm(CertificationsType::class, $certification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('thumbnail_path')->getData();

            if ($file != null) {
                $fileName = $imgUploader->upload($file, "thumbnail_certif");
                $certification->setThumbnailPath($fileName);
            }

            $this->entityManager->persist($certification);
            $this->entityManager->persist(new History($this->user, "modified certification#".$certification->getId()." (title: '".$certification->getTitle()."')"));
            $this->entityManager->flush();

            $this->addFlash('success', 'Successfully edited certification.');
        }

        return $this->render('@mod_root/certifs/edit.html.twig', [
            'certif' => $certification,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}/delete", name="certif_delete", methods={"POST"})
     */
    public function delete(Request $request, Certification $certification): Response {
        
        $providerId = $certification->getEProvider()->getId();
        if ($this->isCsrfTokenValid('delete'.$certification->getId(), $request->request->get('_token'))) {
            $this->entityManager->persist(new History($this->user, "removed certification#".$certification->getId()." (title: '".$certification->getTitle()."')"));
            $this->entityManager->remove($certification);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('provider_certifs_list', ["id" =>$providerId], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{certif_id}/add/{exam_id}", name="certif_add_exam", methods={"GET","POST"})
        * @ParamConverter("exam", options={"mapping": {"exam_id" : "id"}})
     */
    public function add_exam($certif_id, Exam $exam): Response {
        
        $exam->setCertification($this->entityManager->getReference(Certification::class, $certif_id));
        $this->entityManager->persist($exam);
        $this->entityManager->flush();

        return $this->redirectToRoute('certif_available_exams_list', ["id" => $certif_id], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{certif_id}/remove/{exam_id}", name="certif_remove_exam", methods={"GET","POST"})
        * @ParamConverter("exam", options={"mapping": {"exam_id" : "id"}})
     */
    public function remove_exam($certif_id, Exam $exam): Response {
        
        $exam->setCertification(null);
        $this->entityManager->persist($exam);
        $this->entityManager->flush();

        return $this->redirectToRoute('certif_exams_list', ["id" => $certif_id], Response::HTTP_SEE_OTHER);
    }
}