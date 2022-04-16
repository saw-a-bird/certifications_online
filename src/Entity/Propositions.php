<?php

namespace App\Entity;

use App\Repository\PropositionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PropositionsRepository::class)
 */
class Propositions
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $proposition;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isCorrect;

    /**
     * @ORM\ManyToOne(targetEntity=Questions::class, inversedBy="propositions")
     */
    private $question;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProposition(): ?string
    {
        return $this->proposition;
    }

    public function setProposition(string $proposition): self
    {
        $this->proposition = $proposition;

        return $this;
    }

    public function setisCorrect(bool $isCorrect): self
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    public function getisCorrect(): ?bool
    {
        return $this->isCorrect;
    }

    public function getQuestion(): ?Questions
    {
        return $this->question;
    }

    public function setQuestion(?Questions $question): self
    {
        $this->question = $question;

        return $this;
    }
}
