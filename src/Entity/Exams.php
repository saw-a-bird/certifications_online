<?php

namespace App\Entity;

use App\Repository\ExamsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ExamsRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Exams
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=150)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity=Certifications::class, inversedBy="exams")
     * @ORM\JoinColumn(nullable=false)
     */
    private $certification;

    /**
     * @ORM\OneToMany(targetEntity=Questions::class, mappedBy="exam", orphanRemoval=true)
     */
    private $questions;

    /**
     * @ORM\OneToMany(targetEntity=Tries::class, mappedBy="exam", orphanRemoval=true)
     */
    private $tries;

    /**
     * @ORM\Column(type="date")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    public function __construct()
    {
        $this->questions = new ArrayCollection();
        $this->tries = new ArrayCollection();
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

    public function getCertification(): ?Certifications
    {
        return $this->certification;
    }

    public function setCertification(?Certifications $certification): self
    {
        $this->certification = $certification;

        return $this;
    }

/**
     * @return Collection|Tries[]
     */
    public function getTries($user): Collection
    {
        $user_tries = new ArrayCollection();

        foreach ( $this->tries as $try ) {
            if ( $try->getUser()->getId() == $user->getId() ) {
                $user_tries[] = $try;
            }
        }
        
        return $user_tries;
    }

    public function addTry(Tries $try): self
    {
        if (!$this->tries->contains($try)) {
            $this->tries[] = $try;
            $try->setExam($this);
        }

        return $this;
    }

    public function removeTry(Tries $try): self
    {
        if ($this->tries->removeElement($try)) {
            // set the owning side to null (unless already changed)
            if ($try->getExam() === $this) {
                $try->setExam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Questions[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function removeAllQuestions(): void
    {
        $this->questions->clear();;
    }

    public function addQuestion(Questions $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
            $question->setExam($this);
        }

        return $this;
    }

    public function removeQuestion(Questions $question): self
    {
        if ($this->questions->removeElement($question)) {
            // set the owning side to null (unless already changed)
            if ($question->getExam() === $this) {
                $question->setExam(null);
            }
        }

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
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created_at = new \DateTime("now");
    }
}
