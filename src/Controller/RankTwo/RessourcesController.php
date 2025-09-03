<?php

namespace App\Controller\RankTwo;

use App\Entity\eProvider;
use App\Entity\Certification;
use App\Entity\Exam;
use App\Entity\ExamPaper;
use App\Entity\Question;

use App\Repository\eProvidersRepository;
use App\Repository\HistoryRepository;
use App\Repository\ExamsRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator
// use Symfony\Component\HttpFoundation\Request; // Nous avons besoin d'accéder à la requête pour obtenir le numéro de page

//https://stackoverflow.com/questions/34463859/symfony-doctrine-pagination
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/moderator/ressources")
 * @IsGranted("ROLE_MODERATOR")
 */
class RessourcesController extends AbstractController
{
    
     /**
     * @Route("/providers", name="ressources_index", methods={"GET"})
     */
    public function providers_index(eProvidersRepository $providersRepository): Response {
        return $this->render('@ressources_dir/list_providers.html.twig', [
            'providers' => $providersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/providers/{id}/exams", name="provider_exams_list", methods={"GET"})
     */
    public function exams_list(eProvider $provider): Response
    {
        return $this->render('@ressources_dir/list_exams.html.twig', [
            'provider' => $provider,
        ]);
    }

    /**
     * @Route("/providers/{id}/certifs", name="provider_certifs_list", methods={"GET"})
     */
    public function certifs_list(eProvider $provider): Response
    {
        return $this->render('@ressources_dir/list_certifs.html.twig', [
            'provider' => $provider,
        ]);
    }

    /**
     * @Route("/certifications/{id}", name="certif_exams_list", methods={"GET"})
     */
    public function by_certif_list(Certification $certification): Response
    {
        return $this->render('@ressources_dir/list_certif_exams.html.twig', [
            'certification' => $certification
        ]);
    }

    /**
     * @Route("/certifications/{id}/available", name="certif_available_exams_list")
     */
    public function by_certif_list_available_exams(Certification $certification, ExamsRepository $examsRepository) {

        return $this->render('@ressources_dir/list_certif_exam_add.html.twig', [
            'certification' => $certification,
            'available_exams' => $examsRepository->findAvailable()
        ]);
    }

    /**
     * @Route("/exams/{id}", name="exam_papers_list", methods={"GET"})
     */
    public function by_exam_list(Exam $exam): Response
    {
        return $this->render('@ressources_dir/list_exam_papers.html.twig', [
            'exam' => $exam
        ]);
    }

    /**
     * @Route("/exams/papers/{id}", name="paper_questions_list", methods={"GET"})
     */
    public function questions_list(ExamPaper $paper): Response
    {
        return $this->render('@ressources_dir/list_questions.html.twig', [
            'paper' => $paper
        ]);
    }

    /**
     * @Route("/exams/papers/{id}/reports", name="paper_reports_list", methods={"GET"})
     */
    public function paper_reports_list(ExamPaper $paper): Response
    {
        return $this->render('@ressources_dir/list_paper_reports.html.twig', [
            'paper' => $paper
        ]);
    }

    /**
     * @Route("/exams/paper/questions/{id}", name="question_propositions_list", methods={"GET"})
     */
    public function propositions_list(Question $question): Response
    {
        return $this->render('@ressources_dir/list_propositions.html.twig', [
            'question' => $question
        ]);
    }

    /**
     * @Route("/history", name="history_list", methods={"GET"})
     */
    public function history_list(HistoryRepository $historyRepository): Response
    {
        return $this->render('@ressources_dir/list_history.html.twig', [
            'history' => $historyRepository->findAll()
        ]);
    }
}