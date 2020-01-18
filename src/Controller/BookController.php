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
    /**
     * @Route("/", name="book_index")
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
            'filterId' => $editor->getId(),
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
            'filterId' => $tag->getId(),
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
            'filterId' => $author->getId(),
        ]);
    }

    /**
     * @Route("/books/add_from_barcode", name="book_add_from_barcode")
     *
     * @param GetBookDetail          $service
     * @param Request                $request
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function addFromBarcode(GetBookDetail $service, Request $request, EntityManagerInterface $manager): Response
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
                $manager->persist($book);
                $manager->flush();
                $this->addFlash('success', 'Le livre "'.$book->getTitle().'" a été créé');
                $url = $this->generateUrl('book_add_from_barcode');
            } catch (\Exception $exception) {
                $this->addFlash('danger', $exception->getMessage());
                $this->addFlash('danger', $exception->getFile().' '.$exception->getLine());
                $this->addFlash('danger', $exception->getTraceAsString());
                $url = $this->generateUrl('book_add');
            }

            return $this->redirect($url);
        }

        return $this->render('book/add_from_barcode.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/books/add", name="book_add")
     *
     * @param Request                $request
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function add(Request $request, EntityManagerInterface $manager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($book);
            $manager->flush();
            $this->addFlash('success', 'Le livre "'.$book->getTitle().'" a été créé');
            $url = $this->getUrlProvenance($request);

            return $this->redirect($url);
        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/books/edit/{book}", name="book_edit")
     *
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @param Book                   $book
     *
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($book);
            $manager->flush();
            $this->addFlash('success', 'Le livre "'.$book->getTitle().'" a été mis à jour');
            $url = $this->getUrlProvenance($request);

            return $this->redirect($url);
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    /**
     * @Route("/books/delete/{book}", name="book_delete")
     *
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @param Book                   $book
     *
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $manager, Book $book): Response
    {
        $form = $this->createFormBuilder()
            ->add('submit', SubmitType::class)
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->remove($book);
            $manager->flush();
            $this->addFlash('danger', 'Le livre "'.$book->getTitle().'" a été supprimé');
            $url = $this->getUrlProvenance($request);

            return $this->redirect($url);
        }

        return $this->render('book/delete.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getUrlProvenance(Request $request): string
    {
        $filterName = $request->get('filter');
        $filterId = $request->get('id_filter');
        switch ($filterName) {
            case 'éditeur':
                $url = $this->generateUrl('book_editor', ['editor' => $filterId]);
                break;
            case 'auteur':
                $url = $this->generateUrl('book_author', ['author' => $filterId]);
                break;
            case 'tag':
                $url = $this->generateUrl('book_tag', ['tag' => $filterId]);
                break;
            default:
                $url = $this->generateUrl('book_index');
        }

        return $url;
    }
}
