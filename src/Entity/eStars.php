<?php

namespace App\Entity;

use App\Repository\eStarsRatesRepository;
use Doctrine\ORM\Mapping as ORM;
use Onurb\Doctrine\ORMMetadataGrapher\Mapping as Grapher;

/**
 * @Grapher\CustomEntityName("Exam Stars")
 * @ORM\Entity(repositoryClass=eStarsRatesRepository::class)
 */
class eStars
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
     * @ORM\Column(type="integer")
     */
    private $stars;

    /**
     * @ORM\ManyToOne(targetEntity=ExamPaper::class, inversedBy="eStars")
     * @ORM\JoinColumn(nullable=false)
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

    public function getStars(): ?int
    {
        return $this->stars;
    }

    public function setStars(int $stars): self
    {
        $this->stars = $stars;

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

    public function __toString() // for examPaper form (custom label)
    {
        return $this->getStars(). ' - ' . $this->getId();
    }
}
