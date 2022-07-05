<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator
use Symfony\Component\HttpFoundation\Request; // Nous avons besoin d'accéder à la requête pour obtenir le numéro de page
use Symfony\Component\HttpFoundation\Response;

use App\Entity\eProvider;
use App\Entity\Certification;
use App\Entity\Exam;
use App\Repository\CertificationsRepository;
use App\Repository\eProvidersRepository;
use App\Repository\ExamsRepository;
use App\Repository\eStarsRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class IndexController extends AbstractController {

    /**
     * @Route("/provider/{pname}", name="provider_view")
     * @ParamConverter("provider", options={"mapping": {"pname" : "name"}})
     */
    public function view_provider(eProvider $provider) {
            
        return $this->render('provider.html.twig', [
            'provider' => $provider
        ]);
    }

    /**
     * @Route("/certification/{id}", name="certif_view")
     */
    public function view_certif(Certification $certification) {

        return $this->render('certification.html.twig', [
            'certif' => $certification
        ]);
    }

    /**
     * @Route("/exam/{id}", name="exam_view")
     * @ParamConverter("exam", options={"mapping": {"id" : "code"}})
     */
    public function view_exam(Exam $exam, eStarsRepository $starsRepository) {

        return $this->render('exam.html.twig', [
            'exam' => $exam,
            'starsRepository' => $starsRepository
        ]);
    }

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, PaginatorInterface $paginator, eProvidersRepository $providersRepository) {
        
        $providers = null;
        $sType = $request->query->get("type");
        $sText = $request->query->get("find");

        if ($sType == null || $sText == "") {
            $querySearch = $providersRepository->pfindAll($sText);
            $providers = $querySearch;
        } else {
            if ($sType == "E") {
                $querySearch = $providersRepository->byECode($sText);
            } else if ($sType == "C") {
                $querySearch = $providersRepository->byCTitle($sText); 
            } else {
                $querySearch = $providersRepository->byProvider($sText);
            }
        }

        $articles = $paginator->paginate(
            $querySearch, // Requête contenant les données à paginer
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page (meaning null, like NVL2 in SQL)
            6 // Nombre de résultats par page
        );

        return $this->render('index.html.twig', [
            'search_text' => $sText,
            'search_type' => $sType,
            'articles' => $articles,
            'providers' => $providers ?: $providersRepository->nameAll()
        ]);
    }

   /**
     * @Route("/exams/search", name="exams_search")
     */
    public function exams_search(Request $request, ExamsRepository $examsRepository) {
        $term = $request->request->get('search');
        $array = $examsRepository->search($term);

        $response = new Response(json_encode($array));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * @Route("/certifications/search", name="certifs_search")
     */
    public function certifs_search(Request $request, CertificationsRepository $certificationsRepository) {
        $term = $request->request->get('search');
        $array = $certificationsRepository->search($term);

        $response = new Response(json_encode($array));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}