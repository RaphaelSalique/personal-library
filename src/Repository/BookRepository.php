<?php

// License proprietary

namespace App\Repository;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class BookRepository.
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    /**
     * BookRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return Book[] Returns the list of all Books objects with Editor, Authors and Tags
     */
    public function listAllBooksWithRelations()
    {
        return $this->createPreQuery()
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Editor $editor
     *
     * @return Book[] Returns the list of all Books objects from Editor
     */
    public function listBooksFromEditor(Editor $editor)
    {
        return $this->createPreQuery()
            ->where('editor.id = :editor')
            ->setParameter('editor', $editor->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Tag $tag
     *
     * @return Book[] Returns the list of all Books objects from Tag
     */
    public function listBooksFromTag(Tag $tag)
    {
        return $this->createPreQuery()
            ->where('tag.id = :tag')
            ->setParameter('tag', $tag->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Author $author
     *
     * @return Book[] Returns the list of all Books objects from Author
     */
    public function listBooksFromAuthor(Author $author)
    {
        return $this->createPreQuery()
            ->where('author.id = :author')
            ->setParameter('author', $author->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createPreQuery()
    {
        return $this->createQueryBuilder('b')
        ->leftJoin('b.editor', 'editor')
        ->addSelect('editor')
        ->leftJoin('b.authors', 'author')
        ->addSelect('author')
        ->leftJoin('b.tags', 'tag')
        ->addSelect('tag');
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
