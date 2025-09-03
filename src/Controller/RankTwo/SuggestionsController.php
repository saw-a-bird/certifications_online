<?php

namespace App\Controller\RankTwo;

use App\Entity\Certification;
use App\Entity\eProvider;
use App\Services\PDFImporter;

use App\Entity\eSuggestion;
use App\Entity\Exam;
use App\Entity\ExamPaper;
use App\Entity\History;
use App\Form\SuggestionType;

use App\Repository\eSuggestionsRepository;
use App\Services\PDFManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/moderator/suggestions")
 * @IsGranted("ROLE_MODERATOR")
 */
class SuggestionsController extends AbstractController
{
    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/count", name="suggestions_count", methods={"GET"})
     */
    public function suggestionsCount(eSuggestionsRepository $suggestionsRepository): Response
    {
        return new Response($suggestionsRepository->countRows());
    }

    /**
     * @Route("/", name="suggestions_index", methods={"GET","POST"})
     */
    public function index(eSuggestionsRepository $suggestionsRepository): Response {
        return $this->render('@mod_root/suggestions/list_suggestions.html.twig', [
            'suggestions' => $suggestionsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/review/{id}", name="sugg_review", methods={"GET","POST"})
     */
    public function review(eSuggestion $suggestion, PDFManager $pdfManager, Request $request, EntityManagerInterface $entityManager): Response {
        $form = $this->createForm(SuggestionType::class, $suggestion, ["pdf_required" => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('upload_pdf')->getData();
            if ($file != null) {
                $filePath = $pdfManager->upload($file, true);
                $_return = PDFImporter::extract($filePath);

                if ($_return["status"] == true) {
                    $suggestion->setPdfFile($filePath);
                    $suggestion->setQuestionsCount($_return["count"]);

                    $this->addFlash('success', 'Found '.$_return["count"].' questions. You have been successfully edited the suggestion. '.$filePath);
                    $persist = true;
                } else {
                    $this->addFlash('error', "Edit failed. Found 0 questions in PDF. Check the format.");
                }
            } else {
                $this->addFlash('success', 'You have been successfully edited the suggestion.');
                $persist = true;
            }

            if (isset($persist)) {
                $entityManager->persist(new History($this->user, "edited user suggestion#".$suggestion->getId()));
                $entityManager->persist($suggestion);
                $entityManager->flush();
            }
        }

        return $this->render('@mod_root/suggestions/review.html.twig', [
            'form' => $form->createView(),
            'suggestion' => $suggestion
        ]);
    }

     /**
     * @Route("/accept/{id}", name="sugg_accept")
     */
    public function accept(eSuggestion $suggestion, ManagerRegistry $doctrine, PDFManager $pdfManager): Response { 
        
        if ($suggestion->getStatus() == null) {
            $examPaper = new ExamPaper();
            $entityManager = $doctrine->getManager();

            /* 
                User
            */

            $user = $suggestion->getCreatedBy();
            $user->addAcceptedSugg();
            $entityManager->persist($user);

            /*
                Paper Creation
            */

            $examPaper->setSuggestedBy($user);
            $examPaper->setQProvider($suggestion->getQProvider());
            $examPaper->setMinsUntil($suggestion->getMinsUntil());
            $entityManager->persist($examPaper);

            /*
                Exam Provider
            */

            $provider = $doctrine->getRepository(eProvider::class)->findOneBy(["name" => $suggestion->getEProvider()]);
            
            if ($provider == null) { // create provider
                $provider = new eProvider();
                $provider->setName($suggestion->getEProvider());
                $entityManager->persist($provider);
            }

            /*
                EXAM and Questions
            */

            $newPath = $pdfManager->moveToMain($suggestion->getPdfPath());
            PDFImporter::import($entityManager, $examPaper, $newPath);
            
            $exam = $doctrine->getRepository(Exam::class)->findOneBy(["code" => $suggestion->getExamCode()]);
            
            if ($exam == null) { // create exam
                $exam = new Exam();
                $exam->setCode($suggestion->getExamCode());
                $exam->setTitle($suggestion->getExamTitle());
            }

            $exam->addExamPaper($examPaper);
            $exam->setEProvider($provider);
            $entityManager->persist($exam);

            /*
                Certification
            */

            if (!empty($suggestion->getCertificationTitle())) {
                $certification = $doctrine->getRepository(Certification::class)->findOneBy(["title" => $suggestion->getCertificationTitle()]);
            
                if ($certification == null) { // create certfication
                    $certification = new Certification();
                    $certification->setTitle($suggestion->getCertificationTitle());
                    $certification->addExam($exam); // add exam

                } elseif (!$certification->getExams()->contains($exam)) {
                    $certification->addExam($exam); // add exam if not exists
                }

                $entityManager->persist($certification);
            }

            /*
                Remove and History
            */

            $entityManager->persist(new History($this->user, "accepted user suggestion#".$suggestion->getId()));

            $entityManager->persist($suggestion->setAccepted());
            $entityManager->flush();

            return $this->redirectToRoute('paper_edit', ["id" => $examPaper->getId()]);
        }

        return $this->redirectToRoute('suggestions_index');
    }

     /**
     * @Route("/reject/{id}", name="sugg_reject")
     */
    public function reject(Request $request, eSuggestion $suggestion, EntityManagerInterface $entityManager): Response { 
        if ($suggestion->getStatus() == null) {
            $reason = $request->request->get("reject_reason");

            $entityManager->persist(new History($this->user, "rejected suggestion #".$suggestion->getId()." due to ".$reason));

            $entityManager->persist($suggestion->setRejected($reason));
            $entityManager->flush();
        }
        return $this->redirectToRoute('suggestions_index');
    }
}