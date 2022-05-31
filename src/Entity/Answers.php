<?php

// namespace App\Entity;

use App\Repository\AnswersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Tries;
use App\Entity\Propositions;

/**
  * @ORM\Entity(repositoryClass=AnswersRepository::class)
*/
class Answers {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Tries::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $try;

    /**
     * @ORM\ManyToOne(targetEntity=Propositions::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $proposition;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSelected;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTry(): Tries
    {
        return $this->try;
    }

    public function setTry(?Tries $try): self
    {
        $this->try = $try;

        return $this;
    }

    public function getProposition(): ?Propositions
    {
        return $this->proposition;
    }

    public function setProposition(?Propositions $proposition): self
    {
        $this->proposition = $proposition;

        return $this;
    }

    public function getIsSelected(): ?bool
    {
        return $this->isSelected;
    }

    public function setIsSelected(bool $isSelected): self
    {
        $this->isSelected = $isSelected;

        return $this;
    }
}
