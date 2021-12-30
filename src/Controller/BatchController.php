<?php

// License proprietary
namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Tag;
use App\Services\BatchUpdate;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BatchController
 */
class BatchController extends AbstractController
{
    /**
     * @Route("/batch", name="batch")
     *
     * @param Request     $request
     *
     * @param BatchUpdate $batchUpdate
     *
     * @return Response
     */
    public function index(Request $request, BatchUpdate $batchUpdate): Response
    {
        $data = [
            'books' => [],
            'authors' => [],
            'tags' => [],
        ];

        $form = $this->createFormBuilder($data)
            ->add('books', EntityType::class, [
                'class' => Book::class,
                'required' => true,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('authors', EntityType::class, [
                'class' => Author::class,
                'required' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'required' => false,
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $batchUpdate->batchUpdate($form->getData());
            $this->addFlash('success', 'Les livres ont été mis à jour');

            return $this->redirectToRoute('batch');
        }

        return $this->render('batch/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
