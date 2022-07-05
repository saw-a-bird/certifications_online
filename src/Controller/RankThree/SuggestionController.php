<?php

namespace App\Controller\RankThree;

use App\Services\FileUploader;
use App\Form\SuggestionType;
use App\Entity\eSuggestion;
use App\Services\PDFImporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/your/suggestions")
 */
class SuggestionController extends AbstractController {

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/new", name="user_sugg_new")
     */
    public function sugg_new(Request $request, PDFImporter $PDFImporter) {
        $suggestion = new eSuggestion();

        $form = $this->createForm(SuggestionType::class, $suggestion, ["pdf_required" => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileData = $form->get('upload_pdf')->getData();
            $filePath = $PDFImporter->upload($fileData, "suggestions");
            $_return = $PDFImporter->extract();

            if ($_return["status"] == true) {
                $suggestion->setPdfFile($filePath);
                $suggestion->setCreatedBy($this->user);
                $suggestion->setQuestionsCount($_return["count"]);
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($suggestion);
                $entityManager->flush();
    
                $this->addFlash('success',  'Success. Found '.$_return["count"].' questions. Your suggestion was sent to our moderators to make the final decision. If you forgot something, you may edit it in your personal space.');
    
                return $this->redirectToRoute('sugg_edit', ['id' =>  $suggestion->getId() ]);

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
    public function sugg_edit(Request $request, eSuggestion $suggestion, PDFImporter $PDFImporter) {
        if ($suggestion->getCreatedBy()->getId() == $this->user->getId()) {
            $form = $this->createForm(SuggestionType::class, $suggestion, ["pdf_required" => false]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $fileData = $form->get('upload_pdf')->getData();
                if ($fileData != null) {
                    $filePath = $PDFImporter->upload($fileData, "suggestions");
                    $_return = $PDFImporter->extract();

                    if ($_return["status"] == true) {
                        $suggestion->setPdfFile($filePath);
                        $suggestion->setQuestionsCount($_return["count"]);

                        $this->addFlash('success', 'Found '.$_return["count"].' questions. You have been successfully edited your Suggestion.');
                        $em->persist($suggestion);
                        $em->flush();
                    } else {
                        $this->addFlash('error', "Edit failed. Found 0 questions in PDF. Check your format.");
                    }
                } else {
                    $this->addFlash('success', 'You have been successfully edited your Suggestion.');
                    $em->persist($suggestion);
                    $em->flush();
                }
            }

            return $this->render('@user/suggestions/edit.html.twig',
                array('form' => $form->createView(), 'suggestion' => $suggestion)
            );
        }

        return $this->redirectToRoute('index', ['page' =>  $request->query->getInt('page', 1) ]);
    }

    /**
     * @Route("/delete/{id}", name="user_sugg_delete")
     */
    public function sugg_delete(Request $request, eSuggestion $suggestion) {
        if ($this->isCsrfTokenValid('delete'.$suggestion->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($suggestion);
            $entityManager->flush();
        }
        
        return $this->redirectToRoute('user_suggs');
    }
}