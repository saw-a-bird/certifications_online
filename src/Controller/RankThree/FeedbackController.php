<?php

namespace App\Controller\RankThree;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/feedback")
 */
class FeedbackController extends AbstractController {

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/new", name="feedback_new")
     */
    public function feedback_new(Request $request) {
        $feedback = new Feedback();

        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $feedback->setCreatedBy($this->user);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($feedback);
            $entityManager->flush();

            $this->addFlash('success',  'Thank you for your valuable feedback. It was successfully sent to our moderators for review.');

            return $this->redirectToRoute('feedback_new');
        }

        return $this->render('@user/feedbacks/new.html.twig',
            array('form' => $form->createView())
        );
    }
}