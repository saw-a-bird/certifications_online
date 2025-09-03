<?php

namespace App\Controller\RankTwo;

use App\Services\ImageUploader;

use App\Entity\eProvider;
use App\Entity\History;
use App\Form\ProvidersType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/moderator/ressources/providers")
 * @IsGranted("ROLE_MODERATOR")
 */
class ProvidersController extends AbstractController
{
    
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager) {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/new", name="providers_new", methods={"GET","POST"})
     */
    public function new(Request $request, ImageUploader $imgUploader): Response {
        $provider = new eProvider();

        $form = $this->createForm(ProvidersType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('thumbnail_path')->getData();

            if ($file != null) {
                $fileName = $imgUploader->upload($file, "thumbnail_provider");
                $provider->setThumbnailPath($fileName);
            }

            $this->entityManager->persist($provider);
            $this->entityManager->persist(new History($this->user, "created new provider#".$provider->getId()." (name: ".$provider->getName().")"));

            $this->entityManager->flush();

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
    public function edit(eProvider $provider, Request $request, ImageUploader $imgUploader): Response {
        $form = $this->createForm(ProvidersType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('thumbnail_path')->getData();

            if ($file != null) {
                $fileName = $imgUploader->upload($file, "thumbnail_provider");
                $provider->setThumbnailPath($fileName);
            }
            

            $this->entityManager->persist($provider);
            $this->entityManager->persist(new History($this->user, "modified provider#".$provider->getId()." (name: ".$provider->getName().")"));
            $this->entityManager->flush();

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
            $this->entityManager->persist(new History($this->user, "deleted provider#".$provider->getId()." (name: ".$provider->getName().")"));
            $this->entityManager->remove($provider);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('ressources_index', [], Response::HTTP_SEE_OTHER);
    }
}