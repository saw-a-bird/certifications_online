<?php

namespace App\Controller\RankThree;

use App\Services\PDFImporter;

use App\Entity\Certifications;
use App\Entity\Exams;
use App\Entity\Questions;
use App\Entity\Signaler;

use App\Form\ExamsType;
use App\Form\QuestionsType;
use App\Form\PDFImportType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Form;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/admin/rankthree/examens")
 * @IsGranted("ROLE_COLLABORATOR")
 */
class ExamsController extends AbstractController
{

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/signals/check/{id}", name="exam_signal_fix", methods={"GET","POST"})
     */
    public function fixed_signal(Signaler $signal): Response {
        if ($this->user->hasRole("ROLE_ADMIN") !== false || $signal->getExam()->getCertification()->getCreatedBy() == $this->user) {

            $entityManager = $this->getDoctrine()->getManager();
            $signal->setIsFixed(!$signal->getIsFixed());
            $entityManager->persist($signal);
            $entityManager->flush();
            
            return $this->render('@exams_collab/signals.html.twig', [
                'exam' => $signal->getExam()
            ]);
        }

        return $this->redirectToRoute('collab_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/new", name="exam_new", methods={"GET","POST"})
     */
    public function enew(Certifications $certification, Request $request): Response {
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

        return $this->render('@exams_collab/new.html.twig', [
            'exam' => $exam,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/reset", name="exam_reset", methods={"GET","POST"})
     */
    public function ereset(Exams $exam): Response {
        if ($this->user->hasRole("ROLE_ADMIN") !== false || $exam->getCertification()->getCreatedBy() == $this->user) {

            $entityManager = $this->getDoctrine()->getManager();
            $exam->removeAllQuestions();

            $entityManager->persist($exam);
            $entityManager->flush();

            return $this->redirectToRoute('exam_edit', ['id' => $exam->getId()]);
        }

        return $this->redirectToRoute('collab_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
     * @Route("/{id}/signals", name="exam_signals", methods={"GET","POST"})
     */
    public function esignals(Exams $exam): Response {
        if ($this->user->hasRole("ROLE_ADMIN") !== false || $exam->getCertification()->getCreatedBy() == $this->user) {
            return $this->render('@exams_collab/signals.html.twig', [
                'exam' => $exam
            ]);
        }

        return $this->redirectToRoute('collab_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/delete", name="exam_delete", methods={"POST"})
     */
    public function edelete(Request $request, Exams $exam): Response
    {       
        if (($this->user->hasRole("ROLE_ADMIN") !== false || $exam->getCertification()->getCreatedBy() == $this->user) &&$this->isCsrfTokenValid('delete'.$exam->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $exam->removeAllQuestions();

            $entityManager->persist($exam->getCertification());
            $entityManager->remove($exam);
            $entityManager->flush();
        }

        return $this->redirectToRoute('collab_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}", name="exam_edit", methods={"GET","POST"})
     */
    public function eedit(Exams $exam, Request $request, PDFImporter $PDFImporter): Response {
        $_import_msg = "";
        $_import_status = null;

        if ($this->user->hasRole("ROLE_ADMIN") !== false || $exam->getCertification()->getCreatedBy() == $this->user) {
                
            $form = $this->createForm(ExamsType::class, $exam);
            $form->handleRequest($request);

            $question = new Questions();
            $question->setExam($exam);

            $form_question = $this->createForm(QuestionsType::class, $question);
            $form_question->handleRequest($request);

            $arrayImport = array();
            $form_import = $this->createForm(PDFImportType::class, $arrayImport);
            $form_import->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($exam);
                $entityManager->flush();
            } elseif ($form_import->isSubmitted() && $form_import->isValid()) {
                $_import_array = $this->pdf_import($exam, $form_import, $PDFImporter);

                $_import_status = $_import_array["status"];
                $_import_msg = $_import_array["message"];

            } elseif ($form_question->isSubmitted() && $form_question->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();


                foreach ($question->getPropositions() as $proposition) {
                    if ($proposition->getProposition() != "") {
                        $entityManager->persist($proposition);
                    } else {
                        $question->removeProposition($proposition);
                    }
                }

                $entityManager->persist($question);

                $exam->setUpdatedAt();
                $entityManager->persist($exam);

                $exam->addQuestion($question);
                $entityManager->persist($exam->getCertification());

                $entityManager->flush();
                return $this->redirectToRoute('exam_edit', ['id' => $exam->getId()]);
            }

            return $this->render('@exams_collab/edit.html.twig', [
                'import_message' => $_import_msg,
                'import_status' => $_import_status,
                
                'exam' => $exam,
                'form' => $form->createView(),
                'form_question' => $form_question->createView(),
                'form_import' => $form_import->createView()
            ]);
        }

        return $this->redirectToRoute('collab_index', [], Response::HTTP_SEE_OTHER);
    }


    /**
        * @Route("/{eid}/{qid}", name="question_edit", methods={"GET","POST"})
        * @ParamConverter("exam", options={"mapping": {"eid" : "id"}})
        * @ParamConverter("question", options={"mapping": {"qid" : "id"}})
     */
    public function qedit(Exams $exam, Questions $question, Request $request, PDFImporter $PDFImporter): Response {
        $_import_msg = "";
        $_import_status = null;

        if ($this->user->hasRole("ROLE_ADMIN") !== false || $exam->getCertification()->getCreatedBy() == $this->user) {
            $form = $this->createForm(ExamsType::class, $exam);
            $form->handleRequest($request);

            $form_question = $this->createForm(QuestionsType::class, $question);
            $form_question->handleRequest($request);

            $arrayImport = array();
            $form_import = $this->createForm(PDFImportType::class, $arrayImport);
            $form_import->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($exam);
                $entityManager->flush();

            } elseif ($form_import->isSubmitted() && $form_import->isValid()) {
                $_import_array = $this->pdf_import($exam, $form_import, $PDFImporter);

                $_import_status = $_import_array["status"];
                $_import_msg = $_import_array["message"];

            } elseif ($form_question->isSubmitted() && $form_question->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

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

                $exam->setUpdatedAt();
                $entityManager->persist($exam);

                $entityManager->flush();
            }

            return $this->render('@exams_collab/edit.html.twig', [
                'exam' => $exam,
                
                'import_message' => $_import_msg,
                'import_status' => $_import_status,

                'form' => $form->createView(),
                'form_question' => $form_question->createView(),
                'form_import' => $form_import->createView(),
                'question_label' => 'Edit'
            ]);
        }

        return $this->redirectToRoute('collab_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
        * @Route("/{eid}/{qid}/delete", name="question_delete", methods={"GET","POST"})
        * @ParamConverter("exam", options={"mapping": {"eid" : "id"}})
        * @ParamConverter("question", options={"mapping": {"qid" : "id"}})
     */
    public function qdelete(Exams $exam, Questions $question, Request $request): Response
    {
        if (($this->user->hasRole("ROLE_ADMIN") !== false || $exam->getCertification()->getCreatedBy() == $this->user) &&$this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $exam->removeQuestion($question);
            $exam->setUpdatedAt();

            $entityManager->remove($question);
            $entityManager->persist($exam->getCertification());
            $entityManager->persist($exam);

            $entityManager->flush();
        }

        return $this->redirectToRoute('exam_edit', ['id' => $exam->getId()]);
    }


    public function pdf_import(Exams $exam, Form $inputType, PDFImporter $PDFImporter) {
        $file = $inputType->get('FileChooser')->getData();

        $_return = $PDFImporter->import($exam, $file);
        $_questions = $_return["questions"];
        
        if (count($_questions) > 0) {
            $entityManager = $this->getDoctrine()->getManager();

            foreach ($_questions as $question) {
                foreach ($question->getPropositions() as $proposition) {
                    $entityManager->persist($proposition);
                }
                $entityManager->persist($question);
            }

            $entityManager->flush();
        }

        return $_return;
    }
}