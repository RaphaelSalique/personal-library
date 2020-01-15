<?php
// Licence proprietary

namespace App\Services;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Repository\AuthorRepository;
use App\Repository\EditorRepository;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class GetBookDetail
 */
class GetBookDetail
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;
    /**
     * @var EditorRepository
     */
    private $editorRepository;
    /**
     * @var AuthorRepository
     */
    private $authorRepository;

    /**
     * GetBookDetail constructor.
     * @param HttpClientInterface $httpClient
     * @param EditorRepository    $editorRepository
     * @param AuthorRepository    $authorRepository
     */
    public function __construct(HttpClientInterface $httpClient, EditorRepository $editorRepository, AuthorRepository $authorRepository)
    {
        $this->httpClient = $httpClient;
        $this->editorRepository = $editorRepository;
        $this->authorRepository = $authorRepository;
    }

    /**
     * @param string $isbn
     *
     * @return Book
     *
     * @throws RuntimeException
     */
    public function isbnToBook(string $isbn): Book
    {
        $response = $this->httpClient->request('GET', 'http://openlibrary.org/api/books?bibkeys=ISBN:9782070407194&jscmd=details&format=json', [
            'query' => [
                'bibkeys' => 'ISBN:'.$isbn,
                'jscmd' => 'details',
                'format' => 'json',
            ],
        ]);

        $content = $response->getContent();
        if (null !== $content && '' !== $content) {
            $contents = json_decode($content, true);
            $subArray = array_pop($contents);
            $detail = $subArray['details'];

            $book = new Book();
            $book->setIsbn($isbn);
            $publishers = $detail['publishers'];
            $publisher = array_pop($publishers);
            $editor = $this->editorRepository->findOneBy(['name' => $publisher]);
            if (null === $editor) {
                $editor = new Editor();
                $editor->setName($publisher);
                $this->editorRepository->save($editor);
            }
            $book->setEditor($editor);
            $book->setTitle($detail['title']);
            $publishDate = date_create_from_format('F j, Y', $detail['publish_date']);
            $book->setPublishedAt($publishDate);
            $authors = $detail['authors'];
            foreach ($authors as $authorDetail) {
                $authorName = $authorDetail['name'];
                $author = $this->authorRepository->findByCompleteName($authorName);
                if (null === $author) {
                    $author = new Author();
                    $nameArray = explode(' ', $authorName);
                    $lastName = array_pop($nameArray);
                    $firstName = join(' ', $nameArray);
                    $author->setName($lastName);
                    $author->setFirstName($firstName);
                    $this->authorRepository->save($author);
                }
                $book->addAuthor($author);
            }

            return $book;
        }

        throw new RuntimeException("Pas de livre correpondant à l'ISBN $isbn !");
    }
}
