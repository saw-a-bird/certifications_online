<?php

namespace App\Controller\RankThree;

use App\Form\SuggestionType;
use App\Entity\eSuggestion;
use App\Services\PDFImporter;
use App\Services\PDFManager;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/your/suggestions")
 */
class SuggestionController extends AbstractController {

    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManager) {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/new", name="user_sugg_new")
     */
    public function sugg_new(Request $request, PDFManager $pdfManager) {
        $suggestion = new eSuggestion();
        
        $form = $this->createForm(SuggestionType::class, $suggestion, ["pdf_required" => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('upload_pdf')->getData();
            $filePath = $pdfManager->upload($file, true);
            $_return = PDFImporter::extract($filePath);

            if ($_return["status"] == true) {
                $suggestion->setPdfFile($filePath);
                $suggestion->setCreatedBy($this->user);
                $suggestion->setQuestionsCount($_return["count"]);
                
                $this->entityManager->persist($suggestion);
                $this->entityManager->flush();
    
                $this->addFlash('success',  'Success. Found '.$_return["count"].' questions. Your suggestion was sent to our moderators to make the final decision. If you forgot something, you may edit it in your personal space.');
            } else {
                $this->addFlash('error', $_return["message"]);
            }
        }

        return $this->render('@user/suggestions/new.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/edit/{id}", name="user_sugg_edit")
     */
    public function user_sugg_edit(Request $request, eSuggestion $suggestion, PDFManager $PDFManager) {
        if ($suggestion->getStatus() == null && $suggestion->getCreatedBy()->getId() == $this->user->getId()) {
            $form = $this->createForm(SuggestionType::class, $suggestion, ["pdf_required" => false]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $file = $form->get('upload_pdf')->getData();
                if ($file != null) {
                    $filePath = $PDFManager->upload($file, true);
                    $_return = PDFImporter::extract($filePath);

                    if ($_return["status"] == true) {
                        $suggestion->setPdfFile($filePath);
                        $suggestion->setQuestionsCount($_return["count"]);

                        $this->addFlash('success', 'Found '.$_return["count"].' questions. You have been successfully edited your Suggestion.');
                        $this->entityManager->persist($suggestion);
                        $this->entityManager->flush();
                    } else {
                        $this->addFlash('error', "Edit failed. Found 0 questions in PDF. Check your format.");
                    }
                } else {
                    $this->addFlash('success', 'You have been successfully edited your Suggestion.');
                    $this->entityManager->persist($suggestion);
                    $this->entityManager->flush();
                }
            }

            return $this->render('@user/suggestions/edit.html.twig',
                array('form' => $form->createView(), 'suggestion' => $suggestion)
            );
        }

        return $this->redirectToRoute('user_suggs');
    }

    /**
     * @Route("/delete/{id}", name="user_sugg_delete")
     */
    public function sugg_delete(Request $request, eSuggestion $suggestion) {
        if ($this->isCsrfTokenValid('delete'.$suggestion->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($suggestion);
            $this->entityManager->flush();
        }
        
        return $this->redirectToRoute('user_suggs');
    }
}