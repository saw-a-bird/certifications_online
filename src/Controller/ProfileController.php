<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ProfileController extends AbstractController {

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/profile/{name}/about", name="user_about")
     */
    public function user_about($name) {
        return $this->renderIfPossible('profile/about.html.twig', $name);
    }

    /**
     * @Route("/profile/{name}/certifications", name="user_certifs")
     */
    public function user_certifs($name) {
        return $this->renderIfPossible('profile/certifications.html.twig', $name);
    }

    /**
     * @Route("/profile/{name}/favourites", name="user_favs")
     */
    public function user_favs($name) {
        return $this->renderIfPossible('profile/favourites.html.twig', $name);
    }
    

    public function renderIfPossible($url, $name) {
        if ($name == $this->user->getUsername()) {
            return $this->render($url, ["unknown" => $this->user]);
        } else {
            $result = $this->getDoctrine()->getRepository(User::class)->loadUserByUsername($name);
            if (is_null($result)) {
                return $this->redirectToRoute('index');
            }
            return $this->render($url, ["unknown" => $result]);
        }
    }
}