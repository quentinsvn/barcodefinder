<?php
// src/Controller/HomeController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\LibraryRepository;
use App\Entity\Library;

class HomeController extends AbstractController
{
   /**
    * @Route("/", name="home")
    */
    public function index(LibraryRepository $libraryRepository, ManagerRegistry $doctrine): Response
    {
        // When form submitted (POST) then redirect to product page with isbn
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $isbn = $_POST['isbn'];
            return $this->redirectToRoute('product', ['isbn' => $isbn]);
        }

        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $libraryRepository->findAll(),
        ]);
    }
}