<?php

namespace App\Controller\RankOne;

use App\Entity\User;
use App\Repository\UsersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/admin/users")
 * @IsGranted("ROLE_ADMIN")
 */
class UsersController extends AbstractController
{

    public function __construct(TokenStorageInterface $tokenStorage) {
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @Route("/", name="users_index", methods={"GET"})
     */
    public function index(UsersRepository $userRepository): Response
    {
        return $this->render('@admin_root/users/index.html.twig', [
            'users' => $userRepository->findAllExcept($this->user->getId()),
        ]);
    }

    /**
     * @Route("/{id}/able", name="users_able")
     */
    public function able(User $user): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $user->setIsBanned(!$user->getIsBanned());
        $entityManager->persist($user);
        $entityManager->flush();


        return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/mod", name="users_mod")
     */
    public function mod(User $user): Response
    {

        $key = $user->hasRole("ROLE_MODERATOR");
        $roles = $user->getRoles();
        if ($key !== false) {
            unset($roles[$key]);
        } else {
            $roles[] = "ROLE_MODERATOR";
        }

        $user->setRoles($roles);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();


        return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
    }
}