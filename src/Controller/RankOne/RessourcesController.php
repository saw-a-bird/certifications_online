<?php

namespace App\Controller\RankOne;

use App\Entity\Providers;
use App\Entity\Certifications;
use App\Entity\Exams;
use App\Entity\Questions;

use App\Form\ProvidersType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/admin/rankone/ressources")
 * @IsGranted("ROLE_ADMIN")
 */
class RessourcesController extends AbstractController
{

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/certifications/{id}", name="certif_exams_list", methods={"GET"})
     */
    public function exams_list(Certifications $certification): Response
    {
        return $this->render('@ressources_mod/list_exams.html.twig', [
            'certification' => $certification
        ]);
    }

    
    /**
     * @Route("/exams/{id}/signals", name="exam_signals_list", methods={"GET"})
     */
    public function signals_list(Exams $exam): Response
    {
        return $this->render('@ressources_mod/list_signals.html.twig', [
            'exam' => $exam
        ]);
    }

    /**
     * @Route("/exams/{id}", name="exam_questions_list", methods={"GET"})
     */
    public function questions_list(Exams $exam): Response
    {
        return $this->render('@ressources_mod/list_questions.html.twig', [
            'exam' => $exam
        ]);
    }

    /**
     * @Route("/questions/{id}", name="question_propositions_list", methods={"GET"})
     */
    public function propositions_list(Questions $question): Response
    {
        return $this->render('@ressources_mod/list_propositions.html.twig', [
            'question' => $question
        ]);
    }


    /**
     * @Route("/providers/new", name="providers_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response {
        $provider = new Providers();
        $form = $this->createForm(ProvidersType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($provider);
            $entityManager->flush();

            return $this->redirectToRoute('mod_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@ressources_mod/new.html.twig', [
            'provider' => $provider,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/providers/{id}/edit", name="providers_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Providers $provider): Response
    {
        $form = $this->createForm(ProvidersType::class, $provider);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mod_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@ressources_mod/edit.html.twig', [
            'provider' => $provider,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/providers/{id}/delete", name="providers_delete", methods={"POST"})
     */
    public function delete(Request $request, Providers $provider): Response
    {
        if ($this->isCsrfTokenValid('delete'.$provider->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($provider);
            $entityManager->flush();
        }

        return $this->redirectToRoute('mod_index', [], Response::HTTP_SEE_OTHER);
    }
}