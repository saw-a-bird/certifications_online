<?php

namespace App\Entity;

use App\Repository\ExamPapersRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

//https://stackoverflow.com/questions/36620607/symfony-and-doctrine-lazy-loading-is-not-working

/**
 * @ORM\Entity(repositoryClass=ExamPapersRepository::class)
 */
class ExamPaper
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $qProvider;

    /**
     * @ORM\OneToMany(targetEntity=eStars::class, mappedBy="examPaper", fetch="EXTRA_LAZY")
     */
    private $eStars;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=true)
     */
    private $suggestedBy;


    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $importedFrom;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isLocked = true;

    /**
     * @ORM\OneToMany(targetEntity=eAttempt::class, mappedBy="examPaper")
     */
    private $tries;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;
    
    /**
     * @ORM\ManyToOne(targetEntity=Exam::class, inversedBy="examPapers")
     */
    private $exam;
    
    /**
     * @ORM\OneToMany(targetEntity=Question::class, mappedBy="examPaper", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity=eReport::class, mappedBy="examPaper", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $reports;

    /**
     * @ORM\Column(type="integer")
     */
    private $stars = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $minsUntil = 30;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->tries = new ArrayCollection();
        $this->reports = new ArrayCollection();
        $this->eStars = new ArrayCollection();
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

    public function getQProvider(): ?string
    {
        return $this->qProvider;
    }

    public function setQProvider(string $qProvider): self
    {
        $this->qProvider = $qProvider;

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
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function removeAllQuestions(): void
    {
        $this->exam->decCountQ($this->questions->count());
        $this->questions->clear();
        
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setExamPaper($this);
            $this->exam->addCountQ(1);
        }

        return $this;
    }

    public function removeQuestion(Question $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getExamPaper() === $this) {
                $question->setExamPaper(null);
                $this->getExam()->decCountQ(1);
            }
        }

        return $this;
    }

     /**
     * @return Collection<int, Signaler>
     */
    public function getReports(): Collection
    {
        return $this->reports;
    }

    public function addReport(eReport $report): self
    {
        if (!$this->reports->contains($report)) {
            $this->reports[] = $report;
            $report->setExamPaper($this);
        }

        return $this;
    }

    public function removeReport(eReport $report): self
    {
        if ($this->reports->removeElement($report)) {
            // set the owning side to null (unless already changed)
            if ($report->getExamPaper() === $this) {
                $report->setExamPaper(null);
            }
        }

        return $this;
    }

    public function getExam(): ?Exam
    {
        return $this->exam;
    }

    public function setExam(?Exam $exam): self
    {
        $this->exam = $exam;

        return $this;
    }

    /**
     * @return Collection<int, eStars>
     */
    public function getEStars(): Collection
    {
        return $this->eStars;
    }

    public function addEStar(eStars $eStar): self
    {
        if (!$this->eStars->contains($eStar)) {
            $this->eStars[] = $eStar;
            $eStar->setExamPaper($this);
            $this->stars += $eStar->getStars();
        }

        return $this;
    }

    public function removeEStar(eStars $eStar): self
    {
        if ($this->eStars->removeElement($eStar)) {
            // set the owning side to null (unless already changed)
            if ($eStar->getExamPaper() === $this) {
                $eStar->setExamPaper(null);
                $this->stars -= $eStar->getStars();
            }
        }

        return $this;
    }

    public function getStars(): ?int
    {
        return $this->stars;
    }


    public function getSuggestedBy(): ?User
    {
        return $this->suggestedBy;
    }

    public function setSuggestedBy(?User $suggestedBy): self
    {
        $this->suggestedBy = $suggestedBy;

        return $this;
    }

    public function getImportedFrom(): ?string
    {
        return $this->importedFrom;
    }

    public function setImportedFrom(?string $importedFrom): self
    {
        $this->importedFrom = $importedFrom;

        return $this;
    }

    public function getCreator(): ?string
    {
        if (isset($this->suggestedBy)) {
            return $this->suggestedBy->getUsername();
        } else {
            return $this->importedFrom;
        }
    }

    public function getIsLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): self
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    public function getMinsUntil(): ?int
    {
        return $this->minsUntil;
    }

    public function setMinsUntil(int $minsUntil): self
    {
        $this->minsUntil = $minsUntil;

        return $this;
    }
}
