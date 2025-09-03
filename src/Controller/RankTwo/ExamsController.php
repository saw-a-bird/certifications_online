<?php

namespace App\Controller\RankTwo;


use App\Entity\eProvider;
use App\Entity\Exam;
use App\Entity\History;
use App\Form\ExamsType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/moderator/ressources/exams")
 * @IsGranted("ROLE_MODERATOR")
 */
class ExamsController extends AbstractController
{

    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager) {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/new/to/{id}", name="exam_new", methods={"GET","POST"})
     */
    public function new(eProvider $provider, Request $request): Response {
        $exam = new Exam();
        $exam->setEProvider($provider);

        $options = ["provider" => $provider];
        $form = $this->createForm(ExamsType::class, $exam, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $history = new History($this->user, "created new exam (code: ".$exam->getCode().")");
            $this->entityManager->persist($exam);
            $this->entityManager->persist($history);
            $this->entityManager->flush();

            $this->addFlash('success', 'Successfully created a new exam.');

            return $this->redirectToRoute('exam_edit', ['id' => $exam->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@mod_root/exams/new.html.twig', [
            'exam' => $exam,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="exam_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Exam $exam): Response {

        $options = ["provider" => $exam->getEProvider()];
        $form = $this->createForm(ExamsType::class, $exam, $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $history = new History($this->user, "edited exam (code: ".$exam->getCode().")");
            $this->entityManager->persist($exam);
            $this->entityManager->persist($history);
            $this->entityManager->flush();

            $this->addFlash('success', 'Successfully edited the exam.');
        }

        return $this->render('@mod_root/exams/edit.html.twig', [
            'exam' => $exam,
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/{id}/delete", name="exam_delete", methods={"POST"})
     */
    public function delete(Request $request, Exam $exam): Response {
        $providerId = $exam->getEProvider()->getId();
        
        if ($this->isCsrfTokenValid('delete'.$exam->getId(), $request->request->get('_token'))) {
            $history = new History($this->user, "deleted exam (code: ".$exam->getCode().")");
            $this->entityManager->persist($history);
            $this->entityManager->remove($exam);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('provider_exams_list', ["id" =>$providerId], Response::HTTP_SEE_OTHER);
    }
}