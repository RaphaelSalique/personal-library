<?php
// Licence proprietary

namespace App\Services;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Repository\AuthorRepository;
use App\Repository\EditorRepository;
use Google_Service_Books;
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
     * @var \Google_Client
     */
    private $client;

    /**
     * GetBookDetail constructor.
     * @param string              $googleKey
     * @param \Google_Client      $client
     * @param HttpClientInterface $httpClient
     * @param EditorRepository    $editorRepository
     * @param AuthorRepository    $authorRepository
     */
    public function __construct(string $googleKey, \Google_Client $client, HttpClientInterface $httpClient, EditorRepository $editorRepository, AuthorRepository $authorRepository)
    {
        $this->httpClient = $httpClient;
        $this->editorRepository = $editorRepository;
        $this->authorRepository = $authorRepository;
        $this->client = $client;
        $this->client->setApplicationName("Client_Library_Examples");
        $this->client->setDeveloperKey($googleKey);
    }

    /**
     * @param string $isbn
     *
     * @return Book
     */
    public function isbnToBook(string $isbn): Book
    {
        $service = new Google_Service_Books($this->client);
        $optParams = [];
        $results = $service->volumes->listVolumes('isbn:'.$isbn, $optParams);

        $items = $results->getItems();

        if (\count($items) > 0) {
            /** @var \Google_Service_Books_Volume $item */
            $item = array_pop($items);
            $selfLink = $item->selfLink;
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
        throw new RuntimeException(sprintf("Pas de livre correspondant à l'ISBN %s - le saisir à la main ?", $isbn));
    }

    /**
     * disable saveData
     */
    public function disableSaveData(): void
    {
        $this->saveDatas = false;
    }
}
