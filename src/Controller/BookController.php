<?php

// License proprietary

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Entity\Tag;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Services\GetBookDetail;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BookController.
 */
class BookController extends AbstractController
{
    private string $twigFilterView = 'book/filter.html.twig';

    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route(path: '/', name: 'default')]
    public function default(): Response
    {
        return $this->redirectToRoute('login');
    }

    #[Route(path: '/books/editor/{editor}', name: 'book_editor')]
    public function editor(BookRepository $bookRepository, Editor $editor): Response
    {
        return $this->render($this->twigFilterView, [
            'books' => $bookRepository->listBooksFromEditor($editor),
            'filterBook' => 'éditeur',
            'filterName' => $editor->getName(),
            'filterId' => $editor->getId(),
        ]);
    }

    #[Route(path: '/books/tag/{tag}', name: 'book_tag')]
    public function tag(BookRepository $bookRepository, Tag $tag): Response
    {
        return $this->render($this->twigFilterView, [
            'books' => $bookRepository->listBooksFromTag($tag),
            'filterBook' => 'tag',
            'filterName' => $tag->getName(),
            'filterId' => $tag->getId(),
        ]);
    }

    #[Route(path: '/books/author/{author}', name: 'book_author')]
    public function author(BookRepository $bookRepository, Author $author): Response
    {
        return $this->render($this->twigFilterView, [
            'books' => $bookRepository->listBooksFromAuthor($author),
            'filterBook' => 'auteur',
            'filterName' => $author->getName(),
            'filterId' => $author->getId(),
        ]);
    }

    #[Route(path: '/books/add_from_barcode', name: 'book_add_from_barcode')]
    public function addFromBarcode(GetBookDetail $service, Request $request): Response
    {
        $data = [
            'isbn' => null,
        ];
        $form = $this->createFormBuilder($data)
            ->add('isbn')
            ->add('save', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dataForm = $form->getData();
            try {
                $book = $service->isbnToBook($dataForm['isbn']);
                $this->manager->persist($book);
                $this->manager->flush();
                $this->addFlash('success', "Le livre \"{$book->getTitle()}\" a été créé");
                $url = $this->generateUrl('book_add_from_barcode');
            } catch (Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
                $this->addFlash('danger', $exception->getFile() . ' ' . $exception->getLine());
                $this->addFlash('danger', $exception->getTraceAsString());
                $url = $this->generateUrl('book_add');
            }

            return $this->redirect($url);
        }
        return $this->render('book/add_from_barcode.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/books/add', name: 'book_add')]
    public function add(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($book);
            $this->manager->flush();
            $this->addFlash('success', "Le livre \"{$book->getTitle()}\" a été créé");
            $url = $this->getUrlProvenance($request);

            return $this->redirect($url);
        }
        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/books/edit/{book}', name: 'book_edit')]
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($book);
            $this->manager->flush();
            $this->addFlash('success', "Le livre \"{$book->getTitle()}\" a été mis à jour");
            $url = $this->getUrlProvenance($request);

            return $this->redirect($url);
        }
        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    #[Route(path: '/books/view/{book}', name: 'book_view')]
    public function view(Request $request, Book $book): Response
    {
        return $this->render('book/view.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route(path: '/books/delete/{book}', name: 'book_delete')]
    public function delete(Request $request, Book $book): Response
    {
        $form = $this->createFormBuilder()
            ->add('submit', SubmitType::class)
            ->getForm()
        ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->remove($book);
            $this->manager->flush();
            $this->addFlash('danger', "Le livre \"{$book->getTitle()}\" a été supprimé");
            $url = $this->getUrlProvenance($request);

            return $this->redirect($url);
        }
        return $this->render('book/delete.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    #[Route(path: '/books/{page}', name: 'book_index')]
    public function index(BookRepository $bookRepository, int $page = 1): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->listAllBooksWithRelations(),
            'page' => $page
        ]);
    }

    private function getUrlProvenance(Request $request): string
    {
        $filterName = $request->get('filter');
        $filterId = $request->get('id_filter');

        return match ($filterName) {
            'éditeur' => $this->generateUrl('book_editor', ['editor' => $filterId]),
            'auteur' => $this->generateUrl('book_author', ['author' => $filterId]),
            'tag' => $this->generateUrl('book_tag', ['tag' => $filterId]),
            default => $this->generateUrl('book_index'),
        };
    }
}
