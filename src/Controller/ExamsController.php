<?php

namespace App\Controller;

use App\Services\FileUploader;

use App\Entity\Certifications;
use App\Entity\Exams;
use App\Entity\Questions;
use App\Form\ExamsType;
use App\Form\QuestionsType;
use App\Repository\CertificationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/admin/examens")
 */
class ExamsController extends AbstractController
{
    /**
     * @Route("/{id}/new", name="exam_new", methods={"GET","POST"})
     */
    public function new(Certifications $certification, Request $request): Response {
        $exam = new Exams();
        $exam->setCertification($certification);

        $form = $this->createForm(ExamsType::class, $exam);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($exam);
            $entityManager->flush();
            return $this->redirectToRoute('exam_edit', ['id' => $exam->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/exams/new.html.twig', [
            'exam' => $exam,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="exam_edit", methods={"GET","POST"})
     */
    public function edit(Exams $exam, Request $request): Response {
        $form = $this->createForm(ExamsType::class, $exam);
        $form->handleRequest($request);

        $question = new Questions();
        $form_question = $this->createForm(QuestionsType::class, $question);
        $form_question->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($exam);
            $entityManager->flush();

        } elseif ($form_question->isSubmitted() && $form_question->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $question->setExam($exam);

            foreach ($question->getPropositions() as $proposition) {
                if ($proposition->getProposition() != "") {
                    $entityManager->persist($proposition);
                } else {
                    $question->removeProposition($proposition);
                }
            }

            $entityManager->persist($question);
            $entityManager->flush();
            return $this->redirectToRoute('exam_edit', ['id' => $exam->getId()]);
        }

        return $this->render('admin/exams/edit.html.twig', [
            'exam' => $exam,
            'form' => $form->createView(),
            'form_question' => $form_question->createView()
        ]);
    }

    /**
     * @Route("/{id}/delete", name="exam_delete", methods={"POST"})
     */
    public function delete(Request $request, Exams $exam): Response
    {
        if ($this->isCsrfTokenValid('delete'.$exam->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($exam);
            $entityManager->flush();
        }

        return $this->redirectToRoute('certif_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
        * @Route("/{eid}/{qid}", name="question_edit", methods={"GET","POST"})
        * @ParamConverter("exam", options={"mapping": {"eid" : "id"}})
        * @ParamConverter("question", options={"mapping": {"qid" : "id"}})
     */
    public function qedit(Exams $exam, Questions $question, Request $request): Response {

        $form = $this->createForm(ExamsType::class, $exam);
        $form->handleRequest($request);

        $form_question = $this->createForm(QuestionsType::class, $question);
        $form_question->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($exam);
            $entityManager->flush();

        } elseif ($form_question->isSubmitted() && $form_question->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $question->setExam($exam);

            foreach ($question->getPropositions() as $proposition) {
                if ($proposition->getProposition() == "") {
                    if ($proposition->getId() != null) {
                        $entityManager->remove($proposition);
                    }
                    $question->removeProposition($proposition);
                } else {
                    $entityManager->persist($proposition);
                }
            }

            $entityManager->persist($question);
            $entityManager->flush();
            return $this->redirectToRoute('exam_edit', ['id' => $exam->getId()]);
        }

        return $this->render('admin/exams/edit.html.twig', [
            'exam' => $exam,
            'form' => $form->createView(),
            'form_question' => $form_question->createView(),
            'question_label' => 'Edit'
        ]);
    }

    /**
        * @Route("/{eid}/{qid}/delete", name="question_delete", methods={"GET","POST"})
        * @ParamConverter("exam", options={"mapping": {"eid" : "id"}})
        * @ParamConverter("question", options={"mapping": {"qid" : "id"}})
     */ 
    public function qdelete(Exams $exam, Questions $question, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            
            foreach ($question->getPropositions() as $proposition) {
                $entityManager->remove($proposition);
            }

            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('exam_edit', ['id' => $exam->getId()]);
    }
}
