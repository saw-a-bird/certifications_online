<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Tries;
use App\Entity\Exams;
use App\Entity\Propositions;

use Symfony\Component\VarDumper\VarDumper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatter;

use App\Repository\CertificationsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/certifications/exams")
 */
class TryController extends AbstractController {
    
    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/start", name="try_start", methods={"POST"})
     */
    public function start(Request $request) {
        $try = new Tries();
        $try->setTriedAt(new \DateTime("now"));

        $this->get('session')->set('current_try', $try);

        $output = new ConsoleOutput();
        $output->setFormatter(new OutputFormatter(true));
        $output->writeln("try started");

        return new Response();
    }

    /**
     * @Route("/submit", name="answers_submit", methods={"POST"})
     */
    public function submit(Request $request) {
        $data = $request->request->all();

        $entityManager = $this->getDoctrine()->getManager();
        $try = $this->get('session')->get('current_try');
        $exam = $this->get('session')->get('current_exam');

        if ($try->getExam() != null) {
            return new Response();
        }

        $try->setExam($entityManager->getReference(Exams::class, $exam->getId()));
        $try->setUser($this->user);
        $try->setScore($data["score"]);
        
        // $output = new ConsoleOutput();
        // $output->setFormatter(new OutputFormatter(true));
        // $output->writeln($exam->getId());

        $try->setTimeTook($try->getTriedAt()->diff(new \DateTime("now")));

        // foreach($data["data"] as $item) { 
            // $answer->setProposition(
            //     $entityManager->getReference(Propositions::class, $item["proposition"])
            // );
            // $answer->setIsSelected($item["is_selected"]);
            //$try->addAnswer($answer);

            // TODO: increment score
        // }

        $entityManager->persist($try);
        $entityManager->flush();

        return new Response();
    }

    /**
     * @Route("/{id}", name="try_exam")
     */
    public function try_exam(Exams $exam): Response {
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

            return $this->render('attempt/attempt.html.twig', ['exam' => $exam, 'questions' => $json_questions]);
       }

       return $this->redirectToRoute('user_attempts', array(
            'id' => $exam->getCertification()->getId(),
        ));
    }

}
