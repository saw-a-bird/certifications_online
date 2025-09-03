<?php

namespace App\Controller\RankTwo;

use App\Entity\Feedback;
use App\Form\FeedbackType;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/moderator/feedbacks")
 * @IsGranted("ROLE_MODERATOR")
 */
class FeedbackController extends AbstractController
{

    /**
     * @Route("/count", name="feedback_count", methods={"GET"})
     */
    public function unreviewedCount(FeedbackRepository $feedbackRepository): Response
    {
        return new Response($feedbackRepository->countRows());
    }

    /**
     * @Route("/", name="feedbacks_index", methods={"GET"})
     */
    public function index(FeedbackRepository $feedbackRepository): Response
    {
        return $this->render('@mod_root/feedbacks/list_feedback.html.twig', [
            'feedbacks' => $feedbackRepository->findAll()
        ]);
    }

    /**
     * @Route("/view/{id}", name="feedback_view", methods={"GET","POST"})
     */
    public function view(Feedback $feedback, Request $request, EntityManagerInterface $entityManager): Response {
        $feedbackClone = clone($feedback);
        $form = $this->createForm(FeedbackType::class, $feedback, ["readOnly" => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formStatus = $form->get("status")->getData();
            if ($formStatus != $feedbackClone->getStatus()) {
                $feedback->setTitle($feedbackClone->getTitle());
                $feedback->setDescription($feedbackClone->getDescription());

                $entityManager->persist($feedback);
                $entityManager->flush();
                $this->addFlash('success', 'Successfully edited status.');
            }
        }

        return $this->render('@mod_root/feedbacks/view.html.twig', [
            'form' => $form->createView(),
            'feedback' => $feedback
        ]);
    }
}