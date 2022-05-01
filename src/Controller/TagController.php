<?php

// License proprietary
namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/tag')]
class TagController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $manager)
    {
    }

    #[Route(path: '/', name: 'tag_index', methods: ['GET'])]
    public function index(TagRepository $tagRepository): Response
    {
        return $this->render('tag/index.html.twig', [
            'tags' => $tagRepository->findAll(),
        ]);
    }

    #[Route(path: '/new', name: 'tag_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($tag);
            $this->manager->flush();

            return $this->redirectToRoute('tag_index');
        }
        return $this->render('tag/new.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/edit', name: 'tag_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(TagType::class, $tag);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->flush();

            return $this->redirectToRoute('tag_index');
        }
        return $this->render('tag/edit.html.twig', [
            'tag' => $tag,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'tag_delete')]
    public function delete(Request $request, Tag $tag): Response
    {
        $form = $this->createFormBuilder()
            ->add('submit', SubmitType::class, ['label' => 'Supprimer', 'attr' => ['class' => 'is-danger']])
            ->getForm()
        ;
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->remove($tag);
            $this->manager->flush();
            $this->addFlash('danger', 'Le tag "' . $tag->getName() . '" a été supprimé');

            return $this->redirectToRoute('tag_index');
        }
        return $this->render('tag/delete.html.twig', [
            'form' => $form->createView(),
            'tag' => $tag,
        ]);
    }
}
