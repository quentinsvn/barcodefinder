<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConditionsController extends AbstractController
{
    #[Route('/conditions', name: 'app_conditions')]
    public function index(): Response
    {
        return $this->render('conditions/index.html.twig', [
            'controller_name' => 'ConditionsController',
        ]);
    }

    #[Route('/confidentiality', name: 'app_confidentiality')]
    public function confidentiality(): Response
    {
        return $this->render('conditions/confidentiality.html.twig', [
            'controller_name' => 'ConditionsController',
        ]);
    }
}
