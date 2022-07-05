<?php

namespace App\Controller\RankThree;

use App\Entity\eStars;
// use App\Entity\ExamPaper;
use App\Repository\eStarsRepository;
use App\Repository\ExamPapersRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/your/votes")
 */
class VoteAPIController extends AbstractController {

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
    * @Route("/add", name="vote_new", methods={"GET", "POST"})
    */
    public function newVote(Request $request, eStarsRepository $starsRepository, ExamPapersRepository $examPaperRepository) {

        $examPaperId = $request->request->getInt("paper");
        $stars = $request->request->getInt("stars");
        
        // if ($stars > 0 && $starsRepository->findStar($this->user->getId(), $examPaperId) == null) {

            $examPaper = $examPaperRepository->find($examPaperId);

            $eStars  = new eStars();
            $eStars->setStars($stars);
            $eStars->setUser($this->user);
            $examPaper->addEStar($eStars);

            $em = $this->getDoctrine()->getManager();
            $em->persist($eStars);
            $em->persist($examPaper);
            $em->flush();

            return new Response("accepted");

        // }

        // return new Response("rejected");
    }
}