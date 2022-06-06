<?php

namespace App\Controller\RankTwo;

use App\Entity\Certifications;

use App\Entity\Providers;

use App\Repository\ProvidersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/admin/ranktwo/ressources")
 * @IsGranted("ROLE_MODERATOR")
 */
class VisualiserController extends AbstractController
{
    /**
     * @Route("/providers", name="mod_index", methods={"GET"})
     */
    public function index(ProvidersRepository $providersRepository): Response {
        return $this->render('@ressources_mod/list_providers.html.twig', [
            'providers' => $providersRepository->findAll(),
        ]);
    }

    /**
     * @Route("/providers/{id}", name="provider_certifs_list", methods={"GET"})
     */
    public function certifs_list(Providers $provider): Response
    {
        return $this->render('@ressources_mod/list_certifs.html.twig', [
            'provider' => $provider,
        ]);
    }

    /**
     * @Route("/{id}/block", name="certif_block", methods={"GET"})
     */
    public function block(Certifications $certification): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $certification->setIsBlocked(!$certification->getIsBlocked());
        $entityManager->persist($certification);
        $entityManager->flush();

        
        return $this->redirectToRoute('provider_certifs_list', [
            'id' => $certification->getProvider()->getId(),
        ]);
    }
}