<?php

namespace App\Entity;

use App\Repository\QuestionsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=QuestionsRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Questions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $title;

    /**
     * @ORM\Column(type="string", type="text", length=65535)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $task;

    /**
     * @ORM\ManyToOne(targetEntity=Exams::class, inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $exam;

    /**
     * @ORM\OneToMany(targetEntity=Propositions::class, mappedBy="question", orphanRemoval=true)
     */
    private $propositions;

    /**
     * @ORM\Column(type="date")
     */
    private $created_at;

    
    public function __construct()
    {
        $this->propositions = new ArrayCollection();
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

    public function getExam(): ?Exams
    {
        return $this->exam;
    }

    public function setExam(?Exams $exam): self
    {
        $this->exam = $exam;

        return $this;
    }

    /**
     * @return Collection|Propositions[]
     */
    public function getPropositions(): Collection
    {
        return $this->propositions;
    }

    public function addProposition(Propositions $proposition): self
    {
        if (!$this->propositions->contains($proposition)) {
            $this->propositions[] = $proposition;
            $proposition->setQuestion($this);
        }

        return $this;
    }

    public function removeProposition(Propositions $proposition): self
    {
        if ($this->propositions->removeElement($proposition)) {
            // set the owning side to null (unless already changed)
            if ($proposition->getQuestion() === $this) {
                $proposition->setQuestion(null);
            }
        }

        return $this;
    }

    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created_at = new \DateTime("now");
    }
}
