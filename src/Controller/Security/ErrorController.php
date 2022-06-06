<?php

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @Route("error", name="_error")
     */
    public function index(Request $request)
    {
        $session = $request->getSession();
        $code = $session->get("error_code");
        $error =  $session->get("error_message");
        $route =  $session->get("error_route");
        $message = "";

        if ($code == 100) {
            $message = "This exam was probably deleted and no longer exists.";
        } else if ($code == 500) {
            $message = "Internal Server Error. The server encountered an unexpected condition that prevented it from fulfilling the request.";
        }

        return $this->render('errors/error.html.twig', [
            'error' => $error,
            'message' => $message,
            'route' => $route
        ]);
    }
}