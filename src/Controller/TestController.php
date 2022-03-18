<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function test1(): Response {        
        return $this->render('main/index.html.twig', ["title" => "Symfony Project"]);
    }
}
