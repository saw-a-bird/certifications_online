<?php

namespace App\Controller\RankTwo;

use App\Services\FileUploader;

use App\Entity\eProvider;
use App\Form\ProvidersType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/moderator/ressources/providers")
 * @IsGranted("ROLE_MODERATOR")
 */
class ProvidersController extends AbstractController
{
    
    /**
     * @Route("/new", name="providers_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response {
        $provider = new eProvider();

        $form = $this->createForm(ProvidersType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $file = $form->get('thumbnail_path')->getData();

            if ($file != null) {
                $fileName = $fileUploader->upload($file, "thumbnail_provider");
                $provider->setThumbnailPath($fileName);
            }
            
            $entityManager->persist($provider);
            $entityManager->flush();

            return $this->redirectToRoute('ressources_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@mod_root/providers/new.html.twig', [
            'provider' => $provider,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="providers_edit", methods={"GET","POST"})
     */
    public function edit(eProvider $provider, Request $request, FileUploader $fileUploader): Response {
        $form = $this->createForm(ProvidersType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $file = $form->get('thumbnail_path')->getData();

            if ($file != null) {
                $fileName = $fileUploader->upload($file, "thumbnail_provider");
                $provider->setThumbnailPath($fileName);
            }
            
            $entityManager->persist($provider);
            $entityManager->flush();

            return $this->redirectToRoute('ressources_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@mod_root/providers/edit.html.twig', [
            'provider' => $provider,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="providers_delete", methods={"POST"})
     */
    public function delete(Request $request, eProvider $provider): Response
    {
        if ($this->isCsrfTokenValid('delete'.$provider->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($provider);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ressources_index', [], Response::HTTP_SEE_OTHER);
    }
}