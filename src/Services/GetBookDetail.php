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
     * @var bool
     */
    private $saveDatas = true;

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
     */
    public function isbnToBook(string $isbn): Book
    {
        $response = $this->httpClient->request('GET', 'https://www.googleapis.com/books/v1/volumes', [
            'query' => [
                'q' => 'isbn:'.$isbn,
            ],
        ]);

        $content = $response->getContent();
        if (null !== $content && '' !== $content) {
            $contents = json_decode($content, true);
            if (!array_key_exists('items', $contents)) {
                throw new RuntimeException(sprintf("Pas de livre correspondant à l'ISBN %s - le saisir à la main ?", $isbn));
            }
            $subArray = array_pop($contents['items']);
            $selfLink = $subArray['selfLink'];
            $response = $this->httpClient->request('GET', $selfLink);
            $content = $response->getContent();
            $contents = json_decode($content, true);
            $detail = $contents['volumeInfo'];

            $book = new Book();
            $book->setIsbn($isbn);
            $publisher = $detail['publisher'];
            $editor = $this->editorRepository->findOneBy(['name' => $publisher]);
            if (null === $editor) {
                $editor = new Editor();
                $editor->setName($publisher);
                if ($this->saveDatas) {
                    $this->editorRepository->save($editor);
                }
            }
            $book->setEditor($editor);
            $book->setTitle($detail['title']);
            $publishDate = date_create_from_format('Y-m-d', $detail['publishedDate']);
            $book->setPublishedAt($publishDate);
            $authors = $detail['authors'];
            foreach ($authors as $authorName) {
                $author = $this->authorRepository->findByCompleteName($authorName);
                if (null === $author) {
                    $author = new Author();
                    $nameArray = explode(' ', $authorName);
                    $lastName = array_pop($nameArray);
                    $firstName = join(' ', $nameArray);
                    $author->setName($lastName);
                    $author->setFirstName($firstName);
                    if ($this->saveDatas) {
                        $this->authorRepository->save($author);
                    }
                }
                $book->addAuthor($author);
            }

            return $book;
        }

        throw new RuntimeException("Pas de livre correpondant à l'ISBN $isbn !");
    }

    /**
     * disable saveData
     */
    public function disableSaveData(): void
    {
        $this->saveDatas = false;
    }
}
