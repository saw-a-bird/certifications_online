<?php

namespace App\Controller\RankTwo;

use App\Services\PDFImporter;

use App\Entity\Exam;
use App\Entity\Question;
use App\Entity\ExamPaper;
use App\Entity\History;

use App\Form\ExamPapersType;
use App\Form\QuestionsType;
use App\Form\PDFImportType;
use App\Form\ReplaceType;
use App\Repository\QuestionsRepository;
use App\Services\PDFManager;
use Doctrine\ORM\EntityManagerInterface;
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

    private $entityManager;
    private $user;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager) {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->entityManager = $entityManager;
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

            $this->entityManager->persist($examPaper);
            $this->entityManager->persist($examPaper->getExam());
            $this->entityManager->persist(new History($this->user, "created new exam paper#".$examPaper->getId()." (code: ".$exam->getCode().")"));

            $this->entityManager->flush();

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

        $history = new History($this->user, "reset all questions of paper#".$examPaper->getId());

        $this->entityManager->persist($examPaper);//update
        $this->entityManager->persist($history);
        $this->entityManager->flush();

        $this->addFlash('success', 'Successfully removed all paper questions.');
        return $this->redirectToRoute('paper_edit', ['id' => $examPaper->getId()]);
    }

    /**
     * @Route("/{id}/edit", name="paper_edit", methods={"GET","POST"})
     */
    public function edit(ExamPaper $examPaper, Request $request, PDFManager $pdfManager, QuestionsRepository $questionsRepository): Response {
        
        $form = $this->createForm(ExamPapersType::class, $examPaper, ["exam" => $examPaper->getExam()]);
        $form->handleRequest($request);

        $form_import = $this->createForm(PDFImportType::class, array());
        $form_import->handleRequest($request);

        $form_replace = $this->createForm(ReplaceType::class, array());
        $form_replace->handleRequest($request);

        $queryQuestion = $request->query->get("question");
        $found = false; $questionLabel = null;
        
        if ($queryQuestion != null) {
            $question = $questionsRepository->find($queryQuestion);
            
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
            $this->entityManager->persist($examPaper);//+update
            $this->entityManager->flush();

            $this->addFlash('success', 'Successfully edited paper info.');

            $this->entityManager->persist(new History($this->user, "modified exam paper#".$examPaper->getId()." (lock: ".$examPaper->getIsLocked().")"));

        } elseif ($form_import->isSubmitted() && $form_import->isValid()) {
            $fullPath = $pdfManager->upload($form_import->get('FileChooser')->getData(), false);

            list($_import_msg, $_import_status) = PDFImporter::import(
                $this->entityManager,
                $examPaper, 
                $fullPath
            );

            if ($_import_status == "success") {
                $this->entityManager->persist(new History($this->user, "modified questions of exam paper#".$examPaper->getId()));
                $this->entityManager->persist($examPaper); //update
                $this->entityManager->flush();
            }

            $this->addFlash($_import_status, $_import_msg);

        } elseif ($form_replace->isSubmitted() && $form_replace->isValid()) {
            $replaceThis = $form_replace->get('replaceThis')->getData();
            $byThat = $form_replace->get('byThat')->getData();
            $usingRegex = $form_replace->get('usingRegex')->getData();
            $occurences = 0;

            function replace($subject, $thisR, $thatR, $isRegex, &$count) {
                if ($isRegex == 1) {
                    return preg_replace("/$thisR/", $thatR, $subject, -1, $count);
                }
                return str_replace($thisR, $thatR, $subject, $count);
            }

            foreach ($examPaper->getQuestions() as $questionR) {
                $counter = 0; $count = 0;

                $questionR->setTask(replace($questionR->getTask(), $replaceThis, $byThat, $usingRegex, $count)); 

                $counter += $count;
                if ($counter > 0) {
                    $occurences += $counter;
                    $this->entityManager->persist($questionR);
                }

                foreach ($questionR->getPropositions() as $proposition) {

                    $proposition->setProposition(replace($proposition->getProposition(), $replaceThis, $byThat, $usingRegex, $count));
                
                    if ($count > 0) {
                        $occurences += $count;
                        $this->entityManager->persist($proposition);
                    }
                }
            }

            $history = new History($this->user, "modified questions in paper#".$examPaper->getId()." using the replace function (search: '".$replaceThis."', replace: '".$byThat."', isRegex: ".($usingRegex ? "true" : "false").", occurences affected: ".$occurences.")");

            $this->entityManager->persist($examPaper); //update
            $this->entityManager->persist($history);
            $this->entityManager->flush();

            $this->addFlash("success", "Successfully changed ".$occurences." occurences in paper#".$examPaper->getId()." (replaceThis: '".$replaceThis."', byThat: '".$byThat."', usingRegex: ".$usingRegex.")");

            if ($found == true) {
                return $this->redirectToRoute('paper_edit', ['id' => $examPaper->getId(), 'question' => $question->getId()]); // restart form
            }
        } elseif ($form_question->isSubmitted()) {
            if ($form_question->isValid()) {

                if ($found == false) { // different approachs
                    foreach ($question->getPropositions() as $proposition) {
                        if ($proposition->getProposition() != "") {
                            $this->entityManager->persist($proposition);
                        } else {
                            $question->removeProposition($proposition);
                        }
                    }

                    $examPaper->addQuestion($question);
                    $this->addFlash('success', 'Successfully added new question.');
                    $this->entityManager->persist(new History($this->user, "modified questions of paper#".$examPaper->getId()));
                    
                } else {
                    foreach ($question->getPropositions() as $proposition) {
                        if ($proposition->getProposition() == "") {
                            if ($proposition->getId() != null) {
                                $this->entityManager->remove($proposition);
                            }
                            $question->removeProposition($proposition);
                        } else {
                            $this->entityManager->persist($proposition);
                        }
                    }

                    $this->addFlash('success', 'Successfully modified the question.');
                }

                $this->entityManager->persist($question);
                $this->entityManager->persist($examPaper); //update
                $this->entityManager->persist(new History($this->user, "modified questions of paper#".$examPaper->getId()));
                $this->entityManager->flush();

                if ($found == false) {
                    return $this->redirectToRoute('paper_edit', ['id' => $examPaper->getId()]); // reset form
                }
            } else if ($found == true) {
                $question->setTitle($oldQuestionTitle); // reset title
            }
        }

        return $this->render('@mod_root/papers/edit.html.twig', [
            'paper' => $examPaper,
            
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
        $examPaper->setIsLocked(!$examPaper->getIsLocked());
        $this->entityManager->persist($examPaper);//+update
        $this->entityManager->flush();

        
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
            $examPaper->removeAllQuestions();

            $this->entityManager->persist(new History($this->user, "deleted exam paper#".$examPaper->getId()." (code: ".$examPaper->getExam()->getCode().")"));

            $this->entityManager->persist($examPaper->getExam());//update
            $this->entityManager->remove($examPaper);

            $this->entityManager->flush();
        }

        return $this->redirectToRoute('exam_papers_list', ["id" => $examId], Response::HTTP_SEE_OTHER);
    }

    /**
        * @Route("/{eid}/{qid}/delete", name="question_delete", methods={"GET","POST"})
        * @ParamConverter("exam", options={"mapping": {"eid" : "id"}})
        * @ParamConverter("question", options={"mapping": {"qid" : "id"}})
     */
    public function qdelete(ExamPaper $examPaper, Question $question, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {

            $this->entityManager->persist(new History($this->user, "modified questions of paper#".$examPaper->getId()));

            $examPaper->removeQuestion($question);
            $this->entityManager->remove($question);

            $this->entityManager->persist($examPaper);//update
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('paper_edit', ['id' => $examPaper->getId()]);
    }
}