<?php

// License proprietary

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AppFixtures.
 */
class AppFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $editors = ['Gallimard', 'Flammarion', "O'Reilly", 'Packt Publishing', 'Eyrolles'];
        $editorsList = [];
        foreach ($editors as $editorName) {
            $editor = new Editor();
            $editor->setName($editorName);
            $manager->persist($editor);
            $editorsList[] = $editor;
        }

        $authors = [
            [
                'firstName' => 'Jean',
                'name' => 'Durand',
            ],
            [
                'firstName' => 'Frédéric',
                'name' => 'Tram',
            ],
            [
                'firstName' => 'Jacqueline',
                'name' => 'Preli',
            ],
            [
                'firstName' => 'Guillaume',
                'name' => 'Zoul',
            ],
        ];
        $authorsList = [];
        foreach ($authors as $authorDetail) {
            $author = new Author();
            $author->setName($authorDetail['name']);
            $author->setFirstName($authorDetail['firstName']);
            $manager->persist($author);
            $authorsList[] = $author;
        }

        $tags = [
            'Technique' => [
                'AdminSys',
                'PHP',
            ],
            'Roman' => [],
            'Sciences' => [
                'Physique',
                'Biologie',
            ],
        ];
        $tagsList = [];
        foreach ($tags as $tagParentName => $tagDetails) {
            $tag = new Tag();
            $tag->setName($tagParentName);
            $manager->persist($tag);
            $tagsList[] = $tag;
            foreach ($tagDetails as $tagName) {
                $tagChild = new Tag();
                $tagChild->setParent($tag);
                $tagChild->setName($tagName);
                $manager->persist($tagChild);
                $tagsList[] = $tagChild;
            }
        }

        $books = [
            [
                'title' => 'Ansible',
                'editor' => 4,
                'abstract' => 'Vous saurez tout sur Ansible !',
                'publishedAt' => \DateTime::createFromFormat('Y-m-d', '2015-01-04'),
                'isbn' => '32132-6456-4545',
                'authors' => 0,
                'tags' => [0, 1],
            ],
            [
                'title' => 'Une étude en violet',
                'editor' => 0,
                'abstract' => 'Une parodie de Sherlock Holmes',
                'publishedAt' => \DateTime::createFromFormat('Y-m-d', '2010-04-05'),
                'isbn' => '32132-6456-5465465',
                'authors' => 1,
                'tags' => [3],
            ],
            [
                'title' => 'PHP7',
                'editor' => 2,
                'abstract' => 'La référence',
                'publishedAt' => \DateTime::createFromFormat('Y-m-d', '2017-05-14'),
                'isbn' => '32132-45444-12121',
                'authors' => 3,
                'tags' => [0, 2],
            ],
            [
                'title' => 'Une longue histoire des minutes',
                'editor' => 1,
                'abstract' => 'Le temps vu par un physcien',
                'publishedAt' => \DateTime::createFromFormat('Y-m-d', '2004-04-11'),
                'isbn' => '32132-45444-2222',
                'authors' => 3,
                'tags' => [4, 5],
            ],
        ];
        foreach ($books as $bookDetail) {
            $book = new Book();
            $book->setTitle($bookDetail['title']);
            $book->setAbstract($bookDetail['abstract']);
            $book->setEditor($editorsList[$bookDetail['editor']]);
            $book->setIsbn($bookDetail['isbn']);
            $book->setPublishedAt($bookDetail['publishedAt']);
            $book->addAuthor($authorsList[$bookDetail['authors']]);
            foreach ($bookDetail['tags'] as $tagIndex) {
                $book->addTag($tagsList[$tagIndex]);
            }
            $manager->persist($book);
        }

        $manager->flush();
    }
}
