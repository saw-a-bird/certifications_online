<?php

namespace App\Entity;

use App\Repository\TriesRepository;
use Doctrine\ORM\Mapping as ORM;

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

    /**
     * @ORM\Column(type="datetime")
     */
    private $tried_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $time_took;

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

    public function getTimeTook(): ?\DateTimeInterface
    {
        return $this->time_took;
    }

    public function setTimeTook(?\DateTimeInterface $time_took): self
    {
        $this->time_took = $time_took;

        return $this;
    }
}
