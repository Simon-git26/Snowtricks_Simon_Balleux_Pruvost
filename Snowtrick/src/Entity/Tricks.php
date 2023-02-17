<?php

namespace App\Entity;

use App\Repository\TricksRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TricksRepository::class)
 */
class Tricks
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $trick_title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $trick_description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $trick_groupe_trick;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $tric_image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $trick_video;

    /**
     * @ORM\Column(type="datetime")
     */
    private $trick_date_create;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="trick_id")
     */
    private $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrickTitle(): ?string
    {
        return $this->trick_title;
    }

    public function setTrickTitle(string $trick_title): self
    {
        $this->trick_title = $trick_title;

        return $this;
    }

    public function getTrickDescription(): ?string
    {
        return $this->trick_description;
    }

    public function setTrickDescription(string $trick_description): self
    {
        $this->trick_description = $trick_description;

        return $this;
    }

    public function getTrickGroupeTrick(): ?string
    {
        return $this->trick_groupe_trick;
    }

    public function setTrickGroupeTrick(string $trick_groupe_trick): self
    {
        $this->trick_groupe_trick = $trick_groupe_trick;

        return $this;
    }

    public function getTricImage(): ?string
    {
        return $this->tric_image;
    }

    public function setTricImage(string $tric_image): self
    {
        $this->tric_image = $tric_image;

        return $this;
    }

    public function getTrickVideo(): ?string
    {
        return $this->trick_video;
    }

    public function setTrickVideo(string $trick_video): self
    {
        $this->trick_video = $trick_video;

        return $this;
    }

    public function getTrickDateCreate(): ?\DateTimeInterface
    {
        return $this->trick_date_create;
    }

    public function setTrickDateCreate(\DateTimeInterface $trick_date_create): self
    {
        $this->trick_date_create = $trick_date_create;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }
}