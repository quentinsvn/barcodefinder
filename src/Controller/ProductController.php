<?php
// src/Controller/ProductController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Entity\User;
use App\Entity\Library;
use Goutte\Client;
use Symfony\Component\Security\Core\Security;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\LibraryRepository;


class ProductController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

   /**
    * @Route("/product/{isbn}", name="product")
    */
    public function find(int $isbn, ManagerRegistry $doctrine, LibraryRepository $libraryRepository): Response
    {
        $client = new Client();
        $crawler = $client->request('GET', 'https://go-upc.com/search?q=' . $isbn);
        
        $categorie = $crawler->filter('.metadata-label')->first()->text();
        $title = $crawler->filter('.product-name')->first()->text();
        $categorie = $crawler->filter('.table > tr:nth-child(4) > td:nth-child(2)')->first()->text();
        $description = $crawler->filter('div > span')->first()->text();

        // This will output the barcode as HTML output to display in the browser
        $generator = new BarcodeGeneratorPNG();
        $barcode = base64_encode($generator->getBarcode($isbn, $generator::TYPE_EAN_13, 3, 100));

        // Fetch image url
        $image = $crawler->filter('.product-image')->first()->attr('src');

        // When click on saveProduct button, save product in database
        if (isset($_POST['saveProduct'])) {
            $user = $this->getUser()->getId();
            $library = new Library();
            $library->setUserid($user);
            $library->setDate(new \DateTime());
            $library->setProductISBN($isbn);
            $library->setProductName($title);
            $library->setProductCategory($categorie);
            $library->setProductImgSrc($image);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($library);
            $entityManager->flush();
        }

        return $this->render('product/index.html.twig', [
            'categorie' => $categorie,
            'description' => $description,
            'title' => $title,
            'img' => $image,
            'isbn' => $isbn,
            'barcode' => $barcode,
            'products' => $libraryRepository->findAll(),
        ]);
    }

    // Create route and return response in json format
    /**
     * @Route("api/product/{isbn}", name="product_json")
    */
    public function findJson(string $isbn): Response
    {
        if ($this->security->isGranted('ROLE_USER')) {
            $generator = new BarcodeGeneratorPNG();
            $client = new Client();
            $crawler = $client->request('GET', 'https://go-upc.com/search?q=' . $isbn);
            $categorie = $crawler->filter('.metadata-label')->first()->text();
            $title = $crawler->filter('.product-name')->first()->text();
            $categorie = $crawler->filter('.table > tr:nth-child(4) > td:nth-child(2)')->first()->text();
            $description = $crawler->filter('div > span')->first()->text();
            $image = $crawler->filter('.product-image')->first()->attr('src');
            $barcode = base64_encode($generator->getBarcode($isbn, $generator::TYPE_EAN_13, 3, 100));

            $data = [
                'categorie' => $categorie,
                'description' => $description,
                'title' => $title,
                'img' => $image,
                'isbn' => $isbn,
                'barcode' => $barcode,
            ];
            return $this->json($data);
        } else {
            return $this->json(['message' => 'Access denied']);
        }
    }
}