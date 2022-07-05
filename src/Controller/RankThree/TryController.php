<?php

namespace App\Controller\RankThree;

use App\Entity\eAttempt;
use App\Entity\ExamPaper;
use App\Entity\eReport;
use App\Entity\Question;
use App\Repository\ExamPapersRepository;
use App\Repository\QuestionsRepository;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/attempt")
 * @IsGranted("ROLE_USER")
 */
class TryController extends AbstractController {

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/timerstart", name="try_start", methods={"POST"})
     */
    public function start() {
        $try = new eAttempt();
        $try->setTriedAt(new \DateTime("now"));

        $this->get('session')->set('current_try', $try);

        $output = new ConsoleOutput();
        $output->setFormatter(new OutputFormatter(true));
        $output->writeln("try started");

        return new Response();
    }


    /**
     * @Route("/report", name="user_exampaper_report", methods={"POST"})
     */
    public function report(Request $request) {
        $examPaperId = $this->get('session')->get('current_paper');

        $entityManager = $this->getDoctrine()->getManager();
        $examPaper = $entityManager->getReference(ExamPaper::class, $examPaperId);

        if ($examPaper != null) {
            $report = new eReport();

            $report->setCreatedBy($this->user);
            $report->setExamPaper($examPaper);

            $report->setReason($request->request->get("report"));

            $entityManager->persist($report);
            $entityManager->flush();

            return new Response("Successfully delivered.");
        }

        return new Response("This exam cannot be found in the database anymore. Thank you for visiting our website anyway.");
    }
    
    /**
     * @Route("/submit", name="answers_submit", methods={"POST"})
     */
    public function submit(Request $request) {
        $try  = $this->get('session')->get('current_try');
        $examPaperId = $this->get('session')->get('current_paper');

        if ($try->getExamPaper() != null) { // called function more than once
            return new Response();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $examPaper = $entityManager->getReference(ExamPaper::class, $examPaperId);
        
        if ($examPaper != null) {
            $try->setExamPaper($examPaper);
            $try->setUser($this->user);

            $data = $request->request->all();
            $try->setScore($data["score"]);
            $questionCount = $entityManager->getRepository(Question::class)->questionCount($examPaperId);
            $try->setQuestionCount($questionCount);

            $try->setTimeTook($try->getTriedAt()->diff(new \DateTime("now")));
    
            $entityManager->persist($try);
            $entityManager->flush();

            return new Response("Successfully saved score.");
        }

        return new Response("This exam cannot be found in the database anymore. Thank you for visiting our website anyway.");
    }

    /**
     * @Route("/start/{id}", name="try_exam")
     */
    public function try_exam(Request $request, ExamPaper $examPaper): Response {
        if ($this->isGranted('ROLE_MODERATOR')
         || $examPaper->getIsLocked() == false) {
            if (!empty($examPaper->getQuestions())) {
                $this->get('session')->set('current_paper', $examPaper->getId());
                $exam = $examPaper->getExam();

                $papers = $exam->getExamPapers()->toArray();
                $paper_index = array_search($examPaper, $papers)+1;

                return $this->render('@user/quiz.html.twig', [
                    'paper_index' => $paper_index,
                    'exam' => $exam,
                    'paper' => $examPaper,
                    '_return_route' => $request->query->get("_return_route")
                ]);
            }
            
            return $this->redirectToRoute('_error', array("error_route" => $request->attributes->get('_route'), 'error_code' => 101));
        }

        return $this->redirectToRoute('_error', array("error_route" => $request->attributes->get('_route'), 'error_code' => 102));
    }


    /**
     * @Route("/get", name="exampaper_get_data", methods={"POST"})
     */
    public function exampaper_get_data(QuestionsRepository $questionsRepository): Response {
        $examPaperId = $this->get('session')->get('current_paper');
        $questions = $questionsRepository->findByPaperId($examPaperId);
        shuffle($questions);
        
        $json_questions = array();
        foreach ($questions as $seqID => $question) {
            $json_all_p = array();
            $json_correct_p = array();

            foreach ($question->getPropositions() as $proposition) {
                array_push($json_all_p, array("id" => $proposition->getId(),"proposition" => $proposition->getProposition()));
                array_push($json_correct_p, $proposition->getisCorrect());
            }

            array_push($json_questions, array("id" => $question->getId(), "qnumber" => (1+$seqID), "title" => $question->getTitle(), "task" => $question->getTask(), "options" => $json_all_p, "answers" => $json_correct_p));
        }

        $response = new Response(json_encode($json_questions));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}