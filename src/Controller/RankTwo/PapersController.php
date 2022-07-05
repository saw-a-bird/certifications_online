<?php

namespace App\Controller\RankTwo;

use App\Entity\eReport;
use App\Services\PDFImporter;

use App\Entity\Exam;
use App\Entity\Question;
use App\Entity\ExamPaper;
use App\Entity\History;

use App\Form\ExamPapersType;
use App\Form\QuestionsType;
use App\Form\PDFImportType;
use App\Form\ReplaceType;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/moderator/ressources/papers")
 * @IsGranted("ROLE_MODERATOR")
 */
class PapersController extends AbstractController
{
    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/new/to/{id}", name="paper_new", methods={"GET","POST"})
     */
    public function new(Exam $exam, Request $request): Response {
        $examPaper = new ExamPaper();
        $examPaper->setExam($exam);
        $examPaper->setSuggestedBy($this->user);
        
        $options = ["exam" => $exam];
        $form = $this->createForm(ExamPapersType::class, $examPaper, $options);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($examPaper);

            $history = new History($this->user, "created a new exam (code: ".$exam->getCode().", paper: ".$examPaper->getId().")");
            $entityManager->persist($history);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully created a new paper.');

            return $this->redirectToRoute('paper_edit', ['id' => $examPaper->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('@mod_root/papers/new.html.twig', [
            'paper' => $examPaper,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/reset", name="paper_reset", methods={"GET","POST"})
     */
    public function ereset(ExamPaper $examPaper): Response {
        $examPaper->removeAllQuestions();

        $entityManager = $this->getDoctrine()->getManager();
        $history = new History($this->user, "reset all exam paper questions (code: ".$examPaper->getExam()->getCode().", paper: ".$examPaper->getId().")");
        $entityManager->persist($examPaper);
        $entityManager->persist($history);
        $entityManager->flush();

        $this->addFlash('success', 'Successfully removed all paper questions.');
        return $this->redirectToRoute('paper_edit', ['id' => $examPaper->getId()]);
    }

    /**
     * @Route("/{id}/edit", name="paper_edit", methods={"GET","POST"})
     */
    public function edit(ExamPaper $examPaper, Request $request, PDFImporter $PDFImporter, ManagerRegistry $doctrine): Response {

        $_import_msg = ""; $_import_status = null;
        $entityManager = $doctrine->getManager();
        
        $options = ["exam" => $examPaper->getExam()];
        $form = $this->createForm(ExamPapersType::class, $examPaper, $options);
        $form->handleRequest($request);

        $form_import = $this->createForm(PDFImportType::class, array());
        $form_import->handleRequest($request);

        $form_replace = $this->createForm(ReplaceType::class, array());
        $form_replace->handleRequest($request);

        $queryQuestion = $request->query->get("question");
        $found = false; $questionLabel = null;
        if ($queryQuestion != null) {
            $question = $doctrine->getRepository(Question::class)->find($queryQuestion);
            if ($question != null) {
                $found = true;
                $oldQuestionTitle = $question->getTitle();
                $questionLabel = "Edit";
            }
        }

        if ($found == false) { // if not found
            $question = new Question();
            $question->setExamPaper($examPaper);
        }

        $form_question = $this->createForm(QuestionsType::class, $question);
        $form_question->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($examPaper);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully edited paper info.');

        } elseif ($form_import->isSubmitted() && $form_import->isValid()) {
            list($_import_status, $_import_msg) = $PDFImporter->import(
                $entityManager,
                $examPaper, 
                $form_import->get('FileChooser')->getData(),
                null
            );

            $this->addFlash('success', 'Successfully imported PDF. Please check for inconsistencies.');

        } elseif ($form_replace->isSubmitted() && $form_replace->isValid()) {
            $replaceThis = $form_replace->get('replaceThis')->getData();
            $byThat = $form_replace->get('byThat')->getData();
            $occurences = 0;

            foreach ($examPaper->getQuestions() as $questionR) {
                $counter = 0; $count = 0;

                $questionR->setTask(preg_replace("/$replaceThis/", $byThat, $questionR->getTask(), -1, $count)); $counter += $count;

                if ($counter > 0) {
                    $occurences += $counter;
                    $entityManager->persist($questionR);
                }

                foreach ($questionR->getPropositions() as $proposition) {
                    $proposition->setProposition(preg_replace("/$replaceThis/", $byThat, $proposition->getProposition(), -1, $count)); 
                    if ($count > 0) {
                        $occurences += $count;
                        $entityManager->persist($proposition);
                    }
                }
            }

            $history = new History($this->user, "used regex on paper#".$examPaper->getId()." and replaced ".$occurences." occurances (regex: ".$replaceThis.", byThat: ".$byThat.")");

            $entityManager->persist($history);
            $entityManager->flush();

            $this->addFlash('success', 'Successfully changed '.$occurences.' occurences. Regex: (replace: \''.$replaceThis.'\', by: \''.$byThat.'\')');

            if ($found == true) {
                return $this->redirectToRoute('paper_edit', ['id' => $examPaper->getId(), 'question' => $question->getId()]); // restart form
            }
        } elseif ($form_question->isSubmitted()) {
            if ($form_question->isValid()) {

                if ($found == false) { // different approachs
                    foreach ($question->getPropositions() as $proposition) {
                        if ($proposition->getProposition() != "") {
                            $entityManager->persist($proposition);
                        } else {
                            $question->removeProposition($proposition);
                        }
                    }

                    $examPaper->addQuestion($question);

                    $history = new History($this->user, "added new question to paper#".$examPaper->getId()." (code: ".$examPaper->getExam()->getCode().")");
                    $this->addFlash('success', 'Successfully added new question.');
                } else {
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


                    $history = new History($this->user, "edited question of paper#".$examPaper->getId()." (code: ".$examPaper->getExam()->getCode().")");

                    $this->addFlash('success', 'Successfully edited the question.');
                }

                $entityManager->persist($question);

                $examPaper->setUpdatedAt();
                $entityManager->persist($examPaper);
                
                $entityManager->persist($examPaper->getExam());

                $entityManager->persist($history);
                $entityManager->flush();

                if ($found == false) {
                    return $this->redirectToRoute('paper_edit', ['id' => $examPaper->getId()]); // reset form
                }
            } else if ($found == true) {
                $question->setTitle($oldQuestionTitle); // reset title
            }
        }

        return $this->render('@mod_root/papers/edit.html.twig', [
            'paper' => $examPaper,
            
            'import_message' => $_import_msg,
            'import_status' => $_import_status,
            
            'form' => $form->createView(),
            'form_question' => $form_question->createView(),
            'form_import' => $form_import->createView(),
            'form_replace' => $form_replace->createView(),
            
            'question_label' => $questionLabel
        ]);
    }


    /**
     * @Route("/{id}/lock", name="paper_lock", methods={"GET"})
     */
    public function lock(ExamPaper $examPaper): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $examPaper->setIsLocked(!$examPaper->getIsLocked());
        $entityManager->persist($examPaper);
        $entityManager->flush();

        
        return $this->redirectToRoute('exam_papers_list', [
            'id' => $examPaper->getExam()->getId(),
        ]);
    }

     /**
     * @Route("/{id}/delete", name="paper_delete", methods={"POST"})
     */
    public function epdelete(Request $request, ExamPaper $examPaper): Response
    {   
        $examId = $examPaper->getExam()->getId();

        if ($this->isCsrfTokenValid('delete'.$examPaper->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $examPaper->removeAllQuestions();

            $entityManager->persist($examPaper->getExam());

            $history = new History($this->user, "deleted exam paper (code: ".$examPaper->getExam()->getCode().")");
            $entityManager->persist($history);

            $entityManager->remove($examPaper);

            $entityManager->flush();
        }

        return $this->redirectToRoute('exam_papers_list', ["id" => $examId], Response::HTTP_SEE_OTHER);
    }

    /**
        * @Route("/{eid}/{qid}/delete", name="question_delete", methods={"GET","POST"})
        * @ParamConverter("exam", options={"mapping": {"eid" : "id"}})
        * @ParamConverter("question", options={"mapping": {"qid" : "id"}})
     */
    public function qdelete(ExamPaper $exam, Question $question, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $exam->removeQuestion($question);

            $entityManager->remove($question);
            $entityManager->persist($exam->getExam());
            $entityManager->persist($exam);

            $entityManager->flush();
        }

        return $this->redirectToRoute('paper_edit', ['id' => $exam->getId()]);
    }
}