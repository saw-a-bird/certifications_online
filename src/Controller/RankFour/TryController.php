<?php

namespace App\Controller\RankFour;

use App\Entity\Tries;
use App\Entity\Exams;
use App\Entity\Signaler;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


/**
 * @Route("/account/exam/attempt")
 */
class TryController extends AbstractController {

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/start", name="try_start", methods={"POST"})
     */
    public function start() {
        $try = new Tries();
        $try->setTriedAt(new \DateTime("now"));

        $this->get('session')->set('current_try', $try);

        $output = new ConsoleOutput();
        $output->setFormatter(new OutputFormatter(true));
        $output->writeln("try started");

        return new Response();
    }


    /**
     * @Route("/signaler", name="user_exam_signaler", methods={"POST"})
     */
    public function signaler(Request $request) {
        $exam = $this->get('session')->get('current_exam');

        $entityManager = $this->getDoctrine()->getManager();
        $examFind = $entityManager->getReference(Exams::class, $exam->getId());

        if ($examFind != null) {
            $signaler = new Signaler();

            $signaler->setCreatedBy($this->user);
            $signaler->setExam($examFind);

            $data = $request->request->all();
            $signaler->setReason($data["msg"]);

            $entityManager->persist($signaler);
            $entityManager->flush();

            return new Response("Successfully delivered.");
        }

        return new Response("This exam cannot be found in the database. Whatever you did is invalid.");
    }
    
    /**
     * @Route("/submit", name="answers_submit", methods={"POST"})
     */
    public function submit(Request $request) {
        $try = $this->get('session')->get('current_try');
        $exam = $this->get('session')->get('current_exam');

        if ($try->getExam() != null) { // called function more than once
            return new Response();
        }

        $entityManager = $this->getDoctrine()->getManager();
        $examFind = $entityManager->getReference(Exams::class, $exam->getId());

        if ($examFind != null) {
            $try->setExam($examFind);
            $try->setUser($this->user);

            $data = $request->request->all();
            $try->setScore($data["score"]);

            $try->setTimeTook($try->getTriedAt()->diff(new \DateTime("now")));
    
            $entityManager->persist($try);
            $entityManager->flush();

            return new Response("Successfully saved score.");
        }

        return new Response("This exam cannot be found in the database. Whatever you did is invalid.");
    }

    /**
     * @Route("/{id}", name="try_exam")
     */
    public function try_exam(Exams $exam): Response {

        if ($this->user->getCertifications()->contains($exam->getCertification())) {
            if (count($exam->getQuestions()) > 0) {
                $this->get('session')->set('current_exam', $exam);

                $seq = 1;
                $json_questions = array();
                foreach ($exam->getQuestions() as $question) {
                    $json_all_p = array();
                    $json_correct_p = array();

                    foreach ($question->getPropositions() as $proposition) {
                        array_push($json_all_p, array("id" => $proposition->getId(),"proposition" => $proposition->getProposition()));
                        if ($proposition->getisCorrect()) {
                            array_push($json_correct_p, $proposition->getProposition());
                        }
                    }

                    array_push($json_questions, array("id" => $question->getId(), "seq" => $seq++, "title" => $question->getTitle(), "task" => $question->getTask(), "options" => $json_all_p, "answers" => $json_correct_p));
                }

                return $this->render('@user/quiz.html.twig', ['exam' => $exam, 'questions' => $json_questions]);
            }

            return $this->redirectToRoute('user_attempts', array(
                'id' => $exam->getCertification()->getId(),
            ));
        }

        return $this->redirectToRoute('user_certifs');
    }
}