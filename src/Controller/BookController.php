<?php

// License proprietary

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Editor;
use App\Entity\Tag;
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

    /**
     * @Route("/books/editor/{editor}", name="book_editor")
     *
     * @param BookRepository $bookRepository
     * @param Editor         $editor
     *
     * @return Response
     */
    public function editor(BookRepository $bookRepository, Editor $editor): Response
    {
        return $this->render('book/filter.html.twig', [
            'books' => $bookRepository->listBooksFromEditor($editor),
            'filterBook' => 'éditeur',
            'filterName' => $editor->getName(),
        ]);
    }

    /**
     * @Route("/books/tag/{tag}", name="book_tag")
     *
     * @param BookRepository $bookRepository
     * @param Tag            $tag
     *
     * @return Response
     */
    public function tag(BookRepository $bookRepository, Tag $tag): Response
    {
        return $this->render('book/filter.html.twig', [
            'books' => $bookRepository->listBooksFromTag($tag),
            'filterBook' => 'tag',
            'filterName' => $tag->getName(),
        ]);
    }

    /**
     * @Route("/books/author/{author}", name="book_author")
     *
     * @param BookRepository $bookRepository
     * @param Author         $author
     *
     * @return Response
     */
    public function author(BookRepository $bookRepository, Author $author): Response
    {
        return $this->render('book/filter.html.twig', [
            'books' => $bookRepository->listBooksFromAuthor($author),
            'filterBook' => 'auteur',
            'filterName' => $author->getName(),
        ]);
    }
}
