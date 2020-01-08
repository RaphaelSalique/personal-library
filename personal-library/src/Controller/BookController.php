<?php
namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookController
 * @package App\Controller
 */
class BookController extends AbstractController
{

    /**
     * @Route("/")
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', ['books' => $bookRepository->findAll()]);
    }
}
