<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
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
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tag", inversedBy="tags")
     *
     * @var Tag|null
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Tag", mappedBy="parent")
     *
     * @var Collection<Tag, Tag>
     */
    private $tags;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Book", mappedBy="tags")
     *
     * @var Collection<Tag, Book>
     */
    private $books;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

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

    public function addTag(self $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
            $tag->setParent($this);
        }

        return $this;
    }

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

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->addTag($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            $book->removeTag($this);
        }

        return $this;
    }
}
