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

    /**
     * @Route("/certifications/{id}", name="certif_view")
     */
    public function view(Certifications $certification) {

        return $this->render('certification.html.twig', [
            'certif' => $certification
        ]);
    }

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request, PaginatorInterface $paginator, CertificationsRepository $certificationsRepository) {
        $session = $request->getSession();

        $form = $this->createFormBuilder()
            ->add('searchF', EntityType::class, [
                'class' => Providers::class,
                'choice_label' => 'name',
                'required' => false,
                'placeholder' => 'Name...',
            ])
            ->add('searchC', TextType::class, [
                'required' => false
            ])
            ->add('searchE', TextType::class, [
                'required' => false
            ])
            ->add('tab', HiddenType::class)
            ->add('reset', SubmitType::class, ['label' => 'Reset', ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $type = $form->get('tab')->getData();
            $_searchF = $form->get('searchF')->getData();
            $_searchC = $form->get('searchC')->getData();
            $_searchE = $form->get('searchE')->getData();

            if ($type == "tab-F" && $_searchF != null) {
                $session->set('search_type', "Provider");
                $session->set('search_text', $_searchF->getName());

            } elseif ($type == "tab-C" && $_searchC != "") {
                $session->set('search_type', "Certification");
                $session->set('search_text', $_searchC);
                
            } elseif ($type == "tab-E" && $_searchE != "") {
                $session->set('search_type', "Examen");
                $session->set('search_text', $_searchE);

            } elseif ($form->get('reset')->isClicked()) {
                $session->remove('search_type');
                $session->remove('search_text');
            }

            return $this->redirectToRoute('index');
        }

        // $query = $this->repository->recherche($form['rechercheClient']->getData());
        // Méthode findBy qui permet de récupérer les données avec des critères de filtre et de tri

        if ($session->get('search_type') == null) {
            $donnees = $certificationsRepository->getAllFiltered();
        } elseif ($session->get('search_type') == "Certification") {
            $donnees = $certificationsRepository->byTitle($session->get('search_text'));
        } elseif ($session->get('search_type') == "Examen") {
            $donnees = $certificationsRepository->byExamen($session->get('search_text'));
        } else {
            $donnees = $certificationsRepository->byProvider($session->get('search_text'));
        }
        

        $articles = $paginator->paginate(
            $donnees, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page (meaning null, like NVL2 in SQL)
            6 // Nombre de résultats par page
        );

        // //Compte le nombre d'éléments recherchés
        // $count = count($donnees);
        
        return $this->render('index.html.twig', [
            'form' => $form->createView(),
            'articles' => $articles,
            'search_text' => $session->get('search_text') ?: "",
            'search_type' => $session->get('search_type') ?: ""
        ]);
    }
}