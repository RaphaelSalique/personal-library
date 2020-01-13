<?php

// License proprietary

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookController.
 */
class BookController extends AbstractController
{
    /**
     * @Route("/")
     *
     * @param BookRepository $bookRepository
     *
     * @return Response
     */
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', ['books' => $bookRepository->listAllBooksWithRelations()]);
    }
}
