<?php

namespace App\Controller\RankOne;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/rankone")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{

    /**
     * @Route("/dashboard", name="admin_dashboard", methods={"GET"})
     */
    public function index(ChartBuilderInterface $chartBuilder): Response
    {

        // $labels = [];
        // $datasets = [];
        // $repo = $croissantRepository->findAll();
        // foreach($repo as $data){
        //     $labels[] = $data->getDate()->format('d-m-Y');
        //     $datasets[] = $data->getNumber();
        // }
        // $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        // $chart->setData([
        //     'labels' => $labels,
        //     'datasets' => [
        //         [
        //             'label' => 'My First dataset',
        //             'backgroundColor' => 'rgb(255, 99, 132)',
        //             'borderColor' => 'rgb(255, 99, 132)',
        //             'data' => $datasets,

        //         ]
        //     ],
        // ]);

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            'datasets' => [
                [
                    'label' => 'My First dataset',
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
                    'data' => [0, 10, 5, 2, 20, 30, 45],
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                   'suggestedMin' => 0,
                   'suggestedMax' => 100,
                ],
            ],
        ]);

        return $this->render('superuser/dashboard.html.twig', [
            'chart' => $chart,
        ]);
    }
}