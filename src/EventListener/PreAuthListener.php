<?php


namespace App\EventListener;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PreAuthListener implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user)
    {
        if ($user->getIsBanned()) {
            throw new CustomUserMessageAuthenticationException("This user is banned. Try again at a later date.");
        }
    }

    public function checkPostAuth(UserInterface $user)
    {

    }
}
