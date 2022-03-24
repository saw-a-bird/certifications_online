<?php

namespace App\Entity;

use App\Repository\EnrolledRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EnrolledRepository::class)
 */
class Enrolled
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="enrolleds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Certifications::class, inversedBy="enrolleds")
     * @ORM\JoinColumn(nullable=false)
     */
    private $certification;

    /**
     * @ORM\Column(type="string", length=25)
     */
    private $state = "Enrolled"; // states : (enrolled / completed / revise)

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

    public function getCertification(): ?Certifications
    {
        return $this->certification;
    }

    public function setCertification(?Certifications $certification): self
    {
        $this->certification = $certification;

        return $this;
    }

    public function getState(): ?string {
        return $this->state;
    }

    public function setState(string $state): self {
        $this->state = $state;

        return $this;
    }
}
