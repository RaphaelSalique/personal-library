<?php
// License proprietary

namespace App\Services;

use App\Entity\Book;
use App\Entity\Author;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class AuthorMerge
 */
class AuthorMerge
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
     * @param Author[] $authors
     * @param Author   $master
     */
    public function merge(array $authors, Author $master): void
    {
        $repo = $this->manager->getRepository(Book::class);
        /** @var Author $author */
        foreach ($authors as $author) {
            foreach ($repo->listBooksFromAuthor($author) as $book) {
                $book->removeAuthor($author);
                $book->addAuthor($master);
                $this->manager->persist($book);
            }
        }
        $this->manager->flush();
        foreach ($authors as $author) {
            $this->manager->remove($author);
        }
        $this->manager->flush();
    }
}
