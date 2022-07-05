<?php

namespace App\Controller\RankTwo;

use App\Entity\Certification;
use App\Entity\eProvider;
use App\Services\PDFImporter;
use App\Services\FileUploader;

use App\Entity\eSuggestion;
use App\Entity\Exam;
use App\Entity\Question;
use App\Entity\ExamPaper;
use App\Entity\History;
use App\Form\SuggestionType;

use App\Repository\eSuggestionsRepository;
use App\Repository\eProvidersRepository;
use App\Repository\CertificationsRepository;
use App\Repository\ExamsRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Form;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\File;
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
     * @Route("/edit/{id}", name="sugg_edit", methods={"GET","POST"})
     */
    public function edit(eSuggestion $suggestion, PDFImporter $PDFImporter, Request $request): Response {
        $form = $this->createForm(SuggestionType::class, $suggestion, ["pdf_required" => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $fileData = $form->get('upload_pdf')->getData();
            if ($fileData != null) {
                $fileName = $PDFImporter->upload($fileData, "suggestions");
                $_return = $PDFImporter->extract();

                if ($_return["status"] == true) {
                    $suggestion->setPdfFile($fileName);
                    $suggestion->setQuestionsCount($_return["count"]);

                    $this->addFlash('success', 'Found '.$_return["count"].' questions. You have been successfully edited the suggestion. '.$fileName);
                    $persist = true;
                } else {
                    $this->addFlash('error', "Edit failed. Found 0 questions in PDF. Check the format.");
                }
            } else {
                $this->addFlash('success', 'You have been successfully edited the suggestion.');
                $persist = true;
            }

            if (isset($persist)) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($suggestion);
                $em->flush();
            }
        }

        return $this->render('@mod_root/suggestions/edit.html.twig', [
            'form' => $form->createView(),
            'suggestion' => $suggestion
        ]);
    }

     /**
     * @Route("/accept/{id}", name="sugg_accept")
     */
    public function accept(eSuggestion $suggestion, PDFImporter $PDFImporter, eProvidersRepository $providersRepository, ExamsRepository $examsRepository, CertificationsRepository $certificationsRepository): Response { 

        $examPaper = new ExamPaper();
        $entityManager = $this->getDoctrine()->getManager();

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

        $provider = $providersRepository->findOneBy(["name" => $suggestion->getEProvider()]);
        
        if ($provider == null) { // create provider
            $provider = new eProvider();
            $provider->setName($suggestion->getEProvider());
            $entityManager->persist($provider);
        }

        /*
            EXAM and Questions
        */

        $PDFImporter->import($entityManager, $examPaper, null, $suggestion->getPdfName());

        $exam = $examsRepository->findOneBy(["code" => $suggestion->getExamCode()]);
        
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
            $certification = $certificationsRepository->findOneBy(["title" => $suggestion->getCertificationTitle()]);
        
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

        $entityManager->remove($suggestion);

        $entityManager->persist(new History($this->user, "accepted user suggestion (id: ".$examPaper->getId().")"));

        $entityManager->flush();

        return $this->redirectToRoute('paper_edit', ["id" => $examPaper->getId()]);
    }

     /**
     * @Route("/reject/{id}", name="sugg_reject")
     */
    public function reject(eSuggestion $suggestion): Response { 

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist(new History($this->user, "rejected suggestion (id: ".$suggestion->getId().")"));

        $entityManager->remove($suggestion);
        $entityManager->flush();

        return $this->redirectToRoute('suggestions_index');
    }
}