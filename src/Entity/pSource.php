<?php

namespace App\Entity;

use App\Repository\pSourceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=pSourceRepository::class)
 */
class pSource
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ExamPaper::class, inversedBy="pSources")
     * @ORM\JoinColumn(nullable=false)
     */
    private $examPaper;

    /**
     * @ORM\Column(type="float")
     */
    private $version;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVersion(): ?float
    {
        return $this->version;
    }

    public function setVersion(float $version): self
    {
        $this->version = $version;

        return $this;
    }
}
