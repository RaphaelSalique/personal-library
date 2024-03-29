<?php
// License proprietary
namespace App\Controller;

use App\Entity\Book;
use App\Entity\Editor;
use App\Form\EditorType;
use App\Repository\EditorRepository;
use App\Services\EditorMerge;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/editor")
 */
class EditorController extends AbstractController
{
    /**
     * @Route("/", name="editor_index", methods={"GET"})
     *
     * @param EditorRepository $editorRepository
     *
     * @return Response
     */
    public function index(EditorRepository $editorRepository): Response
    {
        return $this->render('editor/index.html.twig', [
            'editors' => $editorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="editor_new", methods={"GET","POST"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request): Response
    {
        $editor = new Editor();
        $form = $this->createForm(EditorType::class, $editor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($editor);
            $entityManager->flush();

            return $this->redirectToRoute('editor_index');
        }

        return $this->render('editor/new.html.twig', [
            'editor' => $editor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="editor_edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Editor  $editor
     *
     * @return Response
     */
    public function edit(Request $request, Editor $editor): Response
    {
        $form = $this->createForm(EditorType::class, $editor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('editor_index');
        }

        return $this->render('editor/edit.html.twig', [
            'editor' => $editor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="editor_delete")
     *
     * @param Request                $request
     * @param EntityManagerInterface $manager
     * @param Editor                 $editor
     *
     * @return Response
     */
    public function delete(Request $request, EntityManagerInterface $manager, Editor $editor): Response
    {
        $form = $this->createFormBuilder()
            ->add('submit', SubmitType::class, ['label' => 'Supprimer', 'attr' => ['class' => 'is-danger']])
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->remove($editor);
            $manager->flush();
            $this->addFlash('danger', 'L\'éditeur "'.$editor->getName().'" a été supprimé');

            return $this->redirectToRoute('editor_index');
        }

        return $this->render('editor/delete.html.twig', [
            'form' => $form->createView(),
            'editor' => $editor,
        ]);
    }

    /**
     * @Route("/merge", name="editor_merge", methods={"GET","POST"})
     *
     * @param Request     $request
     * @param EditorMerge $mergeService
     *
     * @return Response
     */
    public function merge(Request $request, EditorMerge $mergeService): Response
    {
        $data = [
            'editors' => [],
            'master' => null,
        ];
        $form = $this->createFormBuilder($data)
            ->add('editors', EntityType::class, [
                'class' => Editor::class,
                'multiple' => true,
                'label' => 'Éditeurs à fusionner (autre que le master)',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('e')
                    ->orderBy('e.name', 'ASC');
                },
            ])
            ->add('master', EntityType::class, [
                'class' => Editor::class,
                'label' => 'Master (les autres éditeurs seront fusionnés en lui)',
                'query_builder' => function (EntityRepository $repo) {
                    return $repo->createQueryBuilder('e')
                        ->orderBy('e.name', 'ASC');
                },
            ])
            ->getForm()
        ;
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $editors = $data['editors'];
            /** @var Editor $master */
            $master = $data['master'];
            $mergeService->merge($editors, $master);

            $this->addFlash('success', 'Les éditeurs ont été fusionnés !');

            return $this->redirectToRoute('editor_index');
        }

        return $this->render('editor/merge.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
