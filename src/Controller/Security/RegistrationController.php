<?php

namespace App\Controller\Security;

use App\Form\UserType;
use App\Entity\User;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/register", name="security_registration")
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordEncoder
     * @param TokenStorageInterface $tokenStorage
     * @return RedirectResponse|Response
     */
    public function registerAction(Request $request, UserPasswordHasherInterface $passwordHasher)
    // , VerifyEmailHelperInterface $verifyEmailHelper
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
            // $user->setUserIP($request->getClientIp());
            $user->setRoles(['ROLE_USER']);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
            // $tokenStorage->setToken($token);

            $this->addFlash('success', 'Congratulations! You have been successfully registered! Please enter your credentials in this form.');

            // $signatureComponents = $verifyEmailHelper->generateSignature(
            //     'security_verify_email',
            //     $user->getId(),
            //     $user->getEmail(),
            //     ['id' => $user->getId()]
            // );

            // $this->addFlash('success', 
            // "Congratulations! You have been successfully registered!\n Only one step left. Please verify your account by clicking on the confirmation link we've sent to your email address.");
            return $this->redirectToRoute('security_login');
        }

        // 'Congratulations! You have been successfully registered! Confirm your email at: '.$signatureComponents->getSignedUrl()
        return $this->render(
            'security/register.html.twig',
            array('form' => $form->createView())
        );
    }


    /**
     * @Route("/verify", name="security_verify_email")
     */
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UsersRepository $userRepository): Response
    {

        $user = $userRepository->find($request->query->get('id'));
        if (!$user) {
            throw $this->createNotFoundException();
        }
        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail(),
            );
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());
            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
        $this->addFlash('success', 'Account Verified! You can now log in.');
        return $this->redirectToRoute('security_login');
    }
}