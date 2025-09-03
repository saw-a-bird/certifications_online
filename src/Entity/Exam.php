<?php

namespace App\Entity;

use App\Repository\ExamsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ExamsRepository::class)
 * @UniqueEntity(fields="code", message="This exam code is already taken.")
 * @UniqueEntity(fields="title", message="This exam title is already taken.")
 * @ORM\HasLifecycleCallbacks
 */
class Exam
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=Certification::class, inversedBy="exams")
     * @ORM\JoinColumn(nullable=true)
     */
    private $certification;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\eProvider", inversedBy="exams", cascade={"persist"})
     */
    private $eProvider;


    /**
     * @ORM\OneToMany(targetEntity=ExamPaper::class, mappedBy="exam", fetch="EXTRA_LAZY")
     */
    private $examPapers;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="writtenOn")
     */
    private $comments;

    /**
     * @ORM\Column(type="integer")
     */
    private $countQ = 0;

    public function __construct()
    {
        $this->examPapers = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }
    
    /**
     * Gets triggered only on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated_at = new \DateTime("now");
    }

    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getCertification(): ?Certification
    {
        return $this->certification;
    }

    public function setCertification(?Certification $certification): self
    {
        $this->certification = $certification;

        return $this;
    }

    public function getEProvider(): ?eProvider
    {
        return $this->eProvider;
    }

    public function setEProvider(?eProvider $eProvider): self
    {
        $this->eProvider = $eProvider;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(): self
    {
        $this->updated_at = new \DateTime("now");

        return $this;
    }

    /**
     * @return Collection<int, ExamPaper>
     */
    public function getExamPapers(): Collection
    {
        return $this->examPapers;
    }

    public function addExamPaper(ExamPaper $examPaper): self
    {
        if (!$this->examPapers->contains($examPaper)) {
            $this->examPapers[] = $examPaper;
            $examPaper->setExam($this);
        }

        return $this;
    }

    public function removeExamPaper(ExamPaper $examPaper): self
    {
        if ($this->examPapers->removeElement($examPaper)) {
            // set the owning side to null (unless already changed)
            if ($examPaper->getExam() === $this) {
                $examPaper->setExam(null);
                $this->decCountQ($examPaper->getQuestions()->count());
            }
        }

        return $this;
    }

/**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setExam($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getExam() === $this) {
                $comment->setExam(null);
            }
        }

        return $this;
    }


    /**
     * @return int|countQ[]
     */

    public function getCountQ(): ?int
    {
        return $this->countQ;
    }

    public function setCountQ(int $countQ): self
    {
        $this->countQ = $countQ;

        return $this;
    }

    public function addCountQ(int $increase): self
    {
        $this->countQ+=$increase;

        return $this;
    }

    public function decCountQ(int $decrease): self
    {
        $this->countQ-=$decrease;

        return $this;
    }
    
    public function __toString() // for examPaper form (custom label)
    {
        return $this->getCode(). ' - ' . $this->getTitle();
    }
}
