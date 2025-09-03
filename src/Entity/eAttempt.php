<?php

namespace App\Entity;

use App\Repository\eAttemptsRepository;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=eAttemptsRepository::class)
 */
class eAttempt
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $tried_at;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $time_took;

    /**
     * @ORM\Column(type="integer")
     */
    private $score;

    /**
     * @ORM\Column(type="integer")
     */
    private $questionCount;

    /**
     * @ORM\ManyToOne(targetEntity=ExamPaper::class, inversedBy="tries")
     */
    private $examPaper;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTriedAt(): ?\DateTimeInterface
    {
        return $this->tried_at;
    }

    public function setTriedAt(\DateTimeInterface $tried_at): self
    {
        $this->tried_at = $tried_at;

        return $this;
    }

    public function getTimeTook(): ?\DateInterval
    {
        return $this->time_took;
    }

    public function setTimeTook(?\DateInterval $time_took): self
    {
        $this->time_took = $time_took;

        return $this;
    }

    public function getQuestionCount(): ?int
    {
        return $this->questionCount;
    }

    public function setQuestionCount(int $questionCount): self
    {
        $this->questionCount = $questionCount;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }
    public function getExamPaper(): ?ExamPaper
    {
        return $this->examPaper;
    }

    public function setExamPaper(?ExamPaper $examPaper): self
    {
        $this->examPaper = $examPaper;

        return $this;
    }
}
