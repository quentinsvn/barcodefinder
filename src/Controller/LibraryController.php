<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LibraryRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Library;


class LibraryController extends AbstractController
{
    #[Route('/library', name: 'app_library')]
    public function index(LibraryRepository $libraryRepository): Response
    {

        // When click on deleteProduct, delete product in database
        if (isset($_POST['deleteProduct'])) {
            $id = $_POST['id'];
            $entityManager = $this->getDoctrine()->getManager();
            $library = $entityManager->getRepository(Library::class)->find($id);
            $entityManager->remove($library);
            $entityManager->flush();
        }
        
        return $this->render('library/index.html.twig', [
            'controller_name' => 'LibraryController',
            'products' => $libraryRepository->findAll(),
        ]);
    }

    #[Route('/library/delete/{id}', name: 'delete_product')]
    public function delete_product($id, ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $library = $entityManager->getRepository(Library::class)->find($id);
        $entityManager->remove($library);
        $entityManager->flush();
        return $this->redirectToRoute('app_library');
    }
}
