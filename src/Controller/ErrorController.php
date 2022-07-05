<?php

namespace App\Controller;

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
        $code = $request->query->get("error_code");
        $message =  $request->query->get("error_message");
        $route =  $request->query->get("error_route");

        if ($message == null) {
            switch ($code) {
                case 100:
                    $message = "Sorry, this exam was probably deleted and no longer exists."; break;

                case 101:
                    $message = "Sorry, you cannot make an attempt at an exam paper with 0 questions."; break;

                case 102:
                    $message = "Sorry, this exam paper is locked. Come back later."; break;
                default:
                    $message = "Sorry, you cannot access this page.";
            }
        }
        
        return $this->render('errors/error.html.twig', [
            'message' => $message,
            'route' => $route
        ]);
    }
}