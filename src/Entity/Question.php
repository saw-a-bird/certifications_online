<?php

namespace App\Entity;

use App\Repository\QuestionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as Constraints;

/**
 * @ORM\Entity(repositoryClass=QuestionsRepository::class)
 * @Constraints\QConstraint
 */
class Question
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
    private $title;

    /**
     * @ORM\Column(type="string", type="text", length=65535)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $task;

    /**
     * @ORM\OneToMany(targetEntity=Proposition::class, mappedBy="question", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $propositions;

    /**
     * @ORM\ManyToOne(targetEntity=ExamPaper::class, inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $examPaper;
    

    public function __construct()
    {
        $this->propositions = new ArrayCollection();
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTask(): ?string
    {
        return $this->task;
    }

    public function setTask(string $task): self
    {
        $this->task = $task;

        return $this;
    }

    /**
     * @return Collection|Proposition[]
     */
    public function getPropositions(): Collection
    {
        return $this->propositions;
    }

    public function addProposition(Proposition $proposition): self
    {
        if (!$this->propositions->contains($proposition)) {
            $this->propositions[] = $proposition;
            $proposition->setQuestion($this);
        }

        return $this;
    }

    public function removeProposition(Proposition $proposition): self
    {
        if ($this->propositions->removeElement($proposition)) {
            // set the owning side to null (unless already changed)
            if ($proposition->getQuestion() === $this) {
                $proposition->setQuestion(null);
            }
        }

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
