<?php

namespace App\Entity;

use App\Repository\eSuggestionsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=eSuggestionsRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class eSuggestion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="suggestions")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $eProvider;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $qProvider;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $examCode;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $examTitle;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $certificationTitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pdf_file;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 15,
     *      max = 120,
     *      notInRangeMessage = "The exam must be between {{ min }}mins and {{ max }}mins",
     * )
     */
    private $minsUntil = 15;

    /**
     * @ORM\Column(type="integer")
     */
    private $questionsCount;

    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime("now");
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEProvider(): ?string
    {
        return $this->eProvider;
    }

    public function setEProvider(string $eProvider): self
    {
        $this->eProvider = $eProvider;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getExamCode(): ?string
    {
        return $this->examCode;
    }

    public function setExamCode(string $examCode): self
    {
        $this->examCode = $examCode;

        return $this;
    }

    public function getExamTitle(): ?string
    {
        return $this->examTitle;
    }

    public function setExamTitle(string $examTitle): self
    {
        $this->examTitle = $examTitle;

        return $this;
    }

    public function getCertificationTitle(): ?string
    {
        return $this->certificationTitle;
    }

    public function setCertificationTitle(string $certificationTitle): self
    {
        $this->certificationTitle = $certificationTitle;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPdfFile(): ?string
    {
        return "docs/".$this->pdf_file;
    }

    public function getPdfName(): ?string
    {
        return $this->pdf_file;
    }
    
    public function setPdfFile(string $pdf_file): self
    {
        $this->pdf_file = $pdf_file;

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

    public function getQuestionsCount(): ?int
    {
        return $this->questionsCount;
    }

    public function setQuestionsCount(int $questionsCount): self
    {
        $this->questionsCount = $questionsCount;

        return $this;
    }
}
