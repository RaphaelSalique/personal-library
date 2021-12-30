<?php

// Licence proprietary

namespace App\Services;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Editor;
use App\Exception\NoIsbnBookException;
use App\Repository\AuthorRepository;
use App\Repository\EditorRepository;
use DateTimeInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Google_Client;
use Google_Service_Books;
use Google_Service_Books_Volume;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;

use function count;
use function strlen;

/**
 * Class GetBookDetail
 * @SuppressWarnings(PHPMD)
 */
class GetBookDetail
{
    /**
     * @var EditorRepository
     */
    private EditorRepository $editorRepository;
    /**
     * @var AuthorRepository
     */
    private AuthorRepository $authorRepository;
    /**
     * @var bool
     */
    private bool $saveDatas = true;
    /**
     * @var Google_Client
     */
    private Google_Client $client;

    /**
     * GetBookDetail constructor.
     * @param string           $googleKey
     * @param Google_Client    $client
     * @param EditorRepository $editorRepository
     * @param AuthorRepository $authorRepository
     */
    public function __construct(
        string $googleKey,
        Google_Client $client,
        EditorRepository $editorRepository,
        AuthorRepository $authorRepository
    ) {
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
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     */
    public function isbnToBook(string $isbn): Book
    {
        $countryCode = 'FR';
        $service = new Google_Service_Books($this->client);
        $optParams = [];

        $handler = new CurlHandler();
        $stack = HandlerStack::create($handler);
        $stack->push(Middleware::mapRequest(static function ($request) use ($countryCode) {
            return $request->withUri(Uri::withQueryValue(
                $request->getUri(),
                'country',
                $countryCode
            ));
        }));
        $guzzle = new Client([
            'handler' => $stack,
        ]);

        $this->client->setHttpClient($guzzle);
        $results = $service->volumes->listVolumes('isbn:' . $isbn, $optParams);

        $items = $results->getItems();

        if (count($items) > 0) {
            /** @var Google_Service_Books_Volume $item */
            $item = array_pop($items);
            $selfId  = $item->id;
            /** @var Google_Service_Books_Volume $volumeGet */
            $volumeGet = $service->volumes->get($selfId);

            $detail = $volumeGet->getVolumeInfo();
            $book = new Book();
            $book->setIsbn($isbn);
            $publisher = $detail->getPublisher();
            $editor = $this->getEditor($publisher);
            $book->setEditor($editor);
            $book->setTitle($detail->getTitle());

            $publishedDate = $detail->getPublishedDate();
            if (strlen($publishedDate) === 4) {
                $publishedDate .= '-01-01';
            }
            if (strlen($publishedDate) === 7) {
                $publishedDate .= '-01';
            }
            /** @var DateTimeInterface $publishDate */
            $publishDate = date_create_from_format('Y-m-d', $publishedDate);
            $book->setPublishedAt($publishDate);
            $authors = $detail->getAuthors();
            foreach ($authors as $authorName) {
                $author = $this->authorRepository->findByCompleteName($authorName);
                if (null === $author) {
                    $author = new Author();
                    $nameArray = explode(' ', $authorName);
                    $lastName = array_pop($nameArray);
                    $firstName = implode(' ', $nameArray);
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
        throw new NoIsbnBookException(sprintf("Pas de livre correspondant à l'ISBN %s - le saisir à la main ?", $isbn));
    }

    /**
     * disable saveData
     */
    public function disableSaveData(): void
    {
        $this->saveDatas = false;
    }

    /**
     * @param string $publisher
     * @return Editor
     * @throws ORMException
     * @throws \Doctrine\ORM\ORMException
     */
    private function getEditor(string $publisher): Editor
    {
        $editor = $this->editorRepository->findOneBy(['name' => $publisher]);
        if (null === $editor) {
            $editor = new Editor();
            $editor->setName($publisher);
            if ($this->saveDatas) {
                $this->editorRepository->save($editor);
            }
        }
        return $editor;
    }
}
