<?php

// License proprietary

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Editor.
 *
 * @ORM\Entity(repositoryClass="App\Repository\EditorRepository")
 */
class Editor
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Book", mappedBy="editor", orphanRemoval=true)
     *
     * @var Collection<Editor, Book>
     */
    private $books;

    /**
     * Editor constructor.
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
     * @return Collection<Editor, Book>
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
            $book->setEditor($this);
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
            // set the owning side to null (unless already changed)
            if ($book->getEditor() === $this) {
                $book->setEditor(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->name;
    }
}
