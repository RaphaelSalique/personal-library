<?php

// License proprietary
namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use App\Services\AuthorMerge;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/author')]
class AuthorController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route(path: '/', name: 'author_index', methods: ['GET'])]
    public function index(AuthorRepository $authorRepository): Response
    {
        return $this->render('author/index.html.twig', [
            'authors' => $authorRepository->findAll(),
        ]);
    }

    #[Route(path: '/new', name: 'author_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($author);
            $this->manager->flush();

            return $this->redirectToRoute('author_index');
        }
        return $this->render('author/new.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'author_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            return $this->redirectToRoute('author_index');
        }
        return $this->render('author/edit.html.twig', [
            'author' => $author,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'author_delete')]
    public function delete(Request $request, Author $author): Response
    {
        $form = $this->createFormBuilder()
            ->add('submit', SubmitType::class, ['label' => 'Supprimer', 'attr' => ['class' => 'is-danger']])
            ->getForm()
        ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->remove($author);
            $this->manager->flush();
            $this->addFlash('danger', 'L\'auteur "' . $author->getName() . '" a été supprimé');

            return $this->redirectToRoute('author_index');
        }
        return $this->render('author/delete.html.twig', [
            'form' => $form->createView(),
            'author' => $author,
        ]);
    }

    #[Route(path: '/merge', name: 'author_merge', methods: ['GET', 'POST'])]
    public function merge(Request $request, AuthorMerge $mergeService): Response
    {
        $data = [
            'authors' => [],
            'master' => null,
        ];
        $form = $this->createFormBuilder($data)
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'multiple' => true,
                'label' => 'Auteurs à fusionner (autre que le master)',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('a')
                        ->orderBy('a.name', 'ASC');
                },
            ])
            ->add('master', EntityType::class, [
                'class' => Author::class,
                'label' => 'Master (les autres auteurs seront fusionnés en lui)',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('a')
                        ->orderBy('a.name', 'ASC');
                },
            ])
            ->getForm()
        ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $authors = $data['authors'];
            /** @var Author $master */
            $master = $data['master'];
            $mergeService->merge($authors, $master);

            $this->addFlash('success', 'Les auteurs ont été fusionnés !');

            return $this->redirectToRoute('author_index');
        }
        return $this->render('author/merge.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
