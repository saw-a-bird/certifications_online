<?php

namespace App\Entity;

use App\Repository\CertificationRatesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CertificationRatesRepository::class)
 */
class CertificationRates
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Certifications::class, inversedBy="certificationRates")
     * @ORM\JoinColumn(nullable=false)
     */
    private $certification;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isLikeOrDisLike;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIsLikeOrDisLike(): ?bool
    {
        return $this->isLikeOrDisLike;
    }

    public function setIsLikeOrDisLike(bool $isLikeOrDisLike): self
    {
        $this->isLikeOrDisLike = $isLikeOrDisLike;

        return $this;
    }
}
