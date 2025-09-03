<?php

namespace App\Controller\RankTwo;

use App\Entity\User;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("/admin/users")
 * @IsGranted("ROLE_MODERATOR")
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
        return $this->render('@mod_root/users/user_list.html.twig', [
            'users' => $userRepository->findAllExcept($this->user->getId()),
        ]);
    }

    /**
     * @Route("/{id}/able", name="users_able")
     * @IsGranted("ROLE_ADMIN")
     */
    public function able(User $user, EntityManagerInterface $entityManager): Response
    {

        $user->setIsBanned(!$user->getIsBanned());
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/mod", name="users_mod")
     * @IsGranted("ROLE_ADMIN")
     */
    public function mod(User $user, EntityManagerInterface $entityManager): Response
    {

        $key = $user->hasRole("ROLE_MODERATOR");
        $roles = $user->getRoles();
        if ($key !== false) {
            unset($roles[$key]);
        } else {
            $roles[] = "ROLE_MODERATOR";
        }

        $user->setRoles($roles);

        $entityManager->persist($user);
        $entityManager->flush();


        return $this->redirectToRoute('users_index', [], Response::HTTP_SEE_OTHER);
    }
}