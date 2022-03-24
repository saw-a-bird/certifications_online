<?php

namespace App\Controller;

use App\Entity\Providers;
use App\Entity\Certifications;
use App\Repository\CertificationsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface; // Nous appelons le bundle KNP Paginator
use Symfony\Component\HttpFoundation\Request; // Nous avons besoin d'accéder à la requête pour obtenir le numéro de page
use Symfony\Component\Form\Extension\Core\Type\{TextType, SubmitType, HiddenType};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormFactoryInterface;

class DefaultController extends AbstractController {

    private $search_text;
    private $search_type;

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, PaginatorInterface $paginator, CertificationsRepository $certificationsRepository) {
        
        $form = $this->createFormBuilder()
            ->add('searchF', EntityType::class, [
                'class' => Providers::class,
                'choice_label' => 'name',
                'required' => false
            ])
            ->add('searchC', TextType::class, [
                'required' => false
            ])
            ->add('tab', HiddenType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $type = $form->get('tab')->getData();
            $_searchF = $form->get('searchF')->getData();
            $_searchC = $form->get('searchC')->getData();

            if ($type == "tab-F" && $_searchF != null) {
                $this->search_type = "Fournisseur";
                $this->search_text = $_searchF->getName();

            } elseif ($type == "tab-C" && $_searchC != "") {
                $this->search_type = "Certification";
                $this->search_text = $_searchC;
            }
        }

        // $query = $this->repository->recherche($form['rechercheClient']->getData());
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri

        if (!isset($this->search_type)) {
            $donnees = $certificationsRepository->findBy([],['creation_date' => 'desc']);
        } elseif ($this->search_type == "Certification") {
            $donnees = $certificationsRepository->byTitle($this->search_text);
        } else {
            $donnees = $certificationsRepository->byProvider($this->search_text);
        }
        

        $articles = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            9 // Nombre de résultats par page
        );

        // //Compte le nombre d'éléments recherchés
        // $count = count($donnees);
        
        return $this->render('index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles,
            'search_text' => $this->search_text ?: "",
            'search_type' => $this->search_type ?: ""
        ]);
    }
}