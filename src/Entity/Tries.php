<?php

namespace App\Entity;

use App\Repository\TriesRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=TriesRepository::class)
 */
class Tries
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
     * @ORM\ManyToOne(targetEntity=Exams::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $exam;

    // /**
    //  * @ORM\OneToMany(targetEntity=Answers::class, mappedBy="try", orphanRemoval=true, cascade={"persist"}) )
    //  */
    // private $answers;

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

    // public function __construct()
    // {
    //     $this->answers = new ArrayCollection();
    // }

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

    public function getExam(): ?Exams
    {
        return $this->exam;
    }

    public function setExam(?Exams $exam): self
    {
        $this->exam = $exam;

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

    //     /**
    //  * @return Collection|Answers[]
    //  */
    // public function getAnswers(): Collection
    // {
    //     return $this->answers;
    // }

    // public function addAnswer(Answers $answer): self
    // {
    //     if (!$this->answers->contains($answer)) {
    //         $this->answers[] = $answer;
    //         $answer->setTry($this);
    //     }

    //     return $this;
    // }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }
}
