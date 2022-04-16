<?php

namespace App\Controller;

use App\Entity\Providers;
use App\Form\ProvidersType;
use App\Repository\ProvidersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/providers")
 */
class ProvidersController extends AbstractController
{
    /**
     * @Route("/", name="providers_index", methods={"GET"})
     */
    public function index(ProvidersRepository $providersRepository): Response {
        return $this->render('admin/providers/index.html.twig', [
            'providers' => $providersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="providers_show", methods={"GET"})
     */
    public function list(Providers $provider): Response
    {
        return $this->render('admin/providers/list.html.twig', [
            'provider' => $provider,
        ]);
    }

    /**
     * @Route("/new", name="providers_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response {
        $provider = new Providers();
        $form = $this->createForm(ProvidersType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($provider);
            $entityManager->flush();

            return $this->redirectToRoute('providers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/providers//new.html.twig', [
            'provider' => $provider,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="providers_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Providers $provider): Response
    {
        $form = $this->createForm(ProvidersType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('providers_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/providers/edit.html.twig', [
            'provider' => $provider,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="providers_delete", methods={"POST"})
     */
    public function delete(Request $request, Providers $provider): Response
    {
        if ($this->isCsrfTokenValid('delete'.$provider->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($provider);
            $entityManager->flush();
        }

        return $this->redirectToRoute('providers_index', [], Response::HTTP_SEE_OTHER);
    }
}
