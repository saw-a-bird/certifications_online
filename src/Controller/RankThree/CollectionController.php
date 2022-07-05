<?php

namespace App\Controller\RankThree;

use App\Entity\Certification;
use App\Entity\Exam;
use App\Entity\ExamPaper;
use App\Repository\ExamsRepository;
use App\Repository\eAttemptsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator

/**
 * @Route("/your")
 */
class CollectionController extends AbstractController {

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/suggestions", name="user_suggs")
     */
    public function user_suggs() {
        return $this->render('@user/collection/suggestions.html.twig');
    }
    
    /**
     * @Route("/favexams", name="user_favs")
     */
    public function user_favs() {
        return $this->render('@user/collection/favourites.html.twig');
    }

    /**
     * @Route("/certifications", name="user_certifs")
     */
    public function user_certifs(Request $request, PaginatorInterface $paginator) {
        $certifs = $paginator->paginate(
            $this->user->getCertifCollection(), // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page (meaning null, like NVL2 in SQL)
            6 // Nombre de résultats par page
        );
        return $this->render('@user/collection/certifications.html.twig',
            array('certifs' => $certifs)
        );
    }

    /**
     * @Route("/exampaper/attempts/{id}", name="list_attempts")
     */
    public function list_attempts(Request $request, ExamPaper $examPaper, eAttemptsRepository $attemptsRepository): Response {

        if ($this->user->getExamCollection()->contains($examPaper->getExam())) {
            return $this->render('@user/list_attempts.html.twig', [
                'attempts' => $attemptsRepository->getAttempts($this->user->getId(), $examPaper->getId()),
                'examPaper' => $examPaper, 
                '_return_route' => $request->request->get("_return_route")
            ]);
        } else {
            return $this->redirectToRoute('_error', array("error_route" => $request->attributes->get('_route')));
        }
    }
    
     /**
     * @Route("/certifications/add/{id}", name="certif_add_coll", methods={"GET"})
     */
    public function coll_add_certif(Certification $certification): Response {

        if (!$this->user->isAddedCertif($certification)) {
            $this->user->addCertification($certification);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($this->user);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('user_certif_select', ['id' =>  $certification->getId() ]);
    }

    /**
     * @Route("/certifications/remove/{id}", name="certif_remove_coll", methods={"GET"})
     */
    public function coll_remove_certif(Certification $certification, Request $request): Response {

        if ($this->user->isAddedCertif($certification)) {
            $this->user->removeCertification($certification);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($this->user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('index', ['page' =>  $request->query->getInt('page', 1) ]);
    }

    /**
     * @Route("/certifications/{id}", name="user_certif_select")
     */
    public function user_certif_select(Request $request, Certification $certification, eAttemptsRepository $attemptsRepository) {

        if ($this->user->isAddedCertif($certification)) {
            return $this->render('@user/collection/select_certif.html.twig',
                array(
                    'attemptsRepository' => $attemptsRepository,
                    'certif' => $certification
                )
            );
        } else {
            return $this->redirectToRoute('_error', array("error_route" => $request->attributes->get('_route')));
        }
    }

     /**
     * @Route("/favexams/add", name="exam_add_coll", methods={"POST"})
     */
    public function coll_add_exam(Request $request, ExamsRepository $examsRepository): Response {

        $examId = $request->request->getInt("exam_id");
        $exam = $examsRepository->find($examId);
        
        if (!$this->user->isAddedExam($exam)) {
            $this->user->addExam($exam);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($this->user);
            $entityManager->flush();
        }
        
        return new Response("favored!");
    }

    /**
     * @Route("/favexams/remove", name="exam_remove_coll", methods={"GET", "POST"})
     */
    public function coll_remove_exam(Request $request, ExamsRepository $examsRepository): Response {

        $examId = $request->request->getInt("exam_id");
        $exam = $examsRepository->find($examId);

        if ($this->user->isAddedExam($exam)) {
            $this->user->removeExam($exam);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($this->user);
            $entityManager->flush();
        }

        return new Response("unfavored!");
    }

    /**
     * @Route("/favexams/{id}", name="user_exam_select")
     */
    public function user_exam_select(Request $request, Exam $exam, eAttemptsRepository $attemptsRepository) {

        if ($this->user->isAddedExam($exam)) {
            return $this->render('@user/collection/select_exam.html.twig',
                array(
                    'attemptsRepository' => $attemptsRepository,
                    'exam' => $exam
                )
            );
        } else {
            return $this->redirectToRoute('_error', array("error_route" => $request->attributes->get('_route')));
        }
    }
}