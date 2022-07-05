<?php

namespace App\Controller\RankTwo;

use App\Repository\eReportsRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/moderator/reports")
 * @IsGranted("ROLE_MODERATOR")
 */
class ReportsController extends AbstractController
{
    /**
     * @Route("/count", name="reports_count", methods={"GET"})
     */
    public function reportsCount(eReportsRepository $reportsRepository): Response
    {
        return new Response($reportsRepository->countRows());
    }

    /**
     * @Route("/", name="reports_index", methods={"GET","POST"})
     */
    public function index(eReportsRepository $reportsRepository): Response {
        return $this->render('@mod_root/reports/list_reports.html.twig', [
            'reports' => $reportsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/change/status", name="report_change_status", methods={"POST"})
     */
    public function report_change_status(eReportsRepository $reportsRepository, Request $request): Response {
        $report = $reportsRepository->find($request->request->get("id"));
        $status = $request->request->get("status");

        if ($status == "-" || $status == "Fixed"|| $status == "Spam") {
            $report->setStatus($status);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($report);
            $entityManager->flush();
            return new Response("Success");
        }

        return new Response("Failed");
    }
}