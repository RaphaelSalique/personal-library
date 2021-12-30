<?php

// License proprietary

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Tag.
 *
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 */
class Tag
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Tag", inversedBy="tags")
     *
     * @var Tag|null
     */
    private ?Tag $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tag", mappedBy="parent")
     *
     * @var Collection&iterable<Tag>
     */
    private $tags;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Book", mappedBy="tags")
     *
     * @var Collection&iterable<Book>
     */
    private $books;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
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
     * @return Tag|null
     */
    public function getParent(): ?self
    {
        return $this->parent;
    }

    /**
     * @param Tag|null $parent
     *
     * @return $this
     */
    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<Tag, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTag(self $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->setParent($this);
        }

        return $this;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function removeTag(self $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
            // set the owning side to null (unless already changed)
            if ($tag->getParent() === $this) {
                $tag->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<Tag, Book>
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
            $book->addTag($this);
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
            $book->removeTag($this);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
