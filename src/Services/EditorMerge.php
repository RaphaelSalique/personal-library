<?php
// License proprietary

namespace App\Services;

use App\Entity\Book;
use App\Entity\Editor;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class EditorMerge
 */
class EditorMerge
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * EditorMerge constructor.
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Editor[] $editors
     * @param Editor   $master
     */
    public function merge(array $editors, Editor $master): void
    {
        $repo = $this->manager->getRepository(Book::class);
        /** @var Editor $editor */
        foreach ($editors as $editor) {
            foreach ($repo->findBy(['editor' => $editor]) as $book) {
                $book->setEditor($master);
                $this->manager->persist($book);
            }
        }
        $this->manager->flush();
        foreach ($editors as $editor) {
            $this->manager->remove($editor);
        }
        $this->manager->flush();
    }
}
