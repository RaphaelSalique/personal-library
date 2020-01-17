<?php
// Licence proprietary

namespace App\Services;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class BatchUpdate
 */
class BatchUpdate
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * BatchUpdate constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $data
     */
    public function batchUpdate(array $data): void
    {
        /** @var Book[] $books */
        $books = $data['books'];
        /** @var Author[] $authors */
        $authors = $data['authors'];
        /** @var Tag[] $tags */
        $tags = $data['tags'];

        foreach ($books as $book) {
            if (\count($authors) > 0) {
                $book->removeAllAuthors();
            }
            if (\count($tags) > 0) {
                $book->removeAllTags();
            }
            $this->entityManager->persist($book);
            if (\count($authors) > 0) {
                foreach ($authors as $author) {
                    $book->addAuthor($author);
                }
            }
            if (\count($tags) > 0) {
                foreach ($tags as $tag) {
                    $book->addTag($tag);
                }
            }
            $this->entityManager->persist($book);
        }

        $this->entityManager->flush();
    }
}
