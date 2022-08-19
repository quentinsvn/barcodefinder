<?php
// src/Controller/PricingController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PricingController extends AbstractController
{
   /**
    * @Route("/pricing", name="pricing")
    */
    public function index(): Response
    {
        return $this->render('pricing/index.html.twig');
    }
}