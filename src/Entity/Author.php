<?php

// License proprietary

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Author.
 *
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private string $firstName;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Book", mappedBy="authors")
     *
     * @var Collection&iterable<Book>
     */
    private $books;

    /**
     * Author constructor.
     */
    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return Collection<Author, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    /**
     * @param Book $book
     *
     * @return $this
     */
    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->addAuthor($this);
        }

        return $this;
    }

    /**
     * @param Book $book
     *
     * @return $this
     */
    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            $book->removeAuthor($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->firstName . ' ' . (string) $this->name;
    }
}
