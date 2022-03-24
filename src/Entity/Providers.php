<?php

namespace App\Entity;

use App\Repository\ProvidersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProvidersRepository::class)
 * @UniqueEntity(fields="name", message="This name is already used.")
 */
class Providers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Certifications::class, mappedBy="provider")
     */
    private $certifications;

    public function __construct() {
        $this->certifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Certifications[]
     */
    public function getCertifications(): Collection
    {
        return $this->certifications;
    }

    public function addCertification(Certifications $certification): self
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications[] = $certification;
            $certification->setProvider($this);
        }

        return $this;
    }

    public function removeCertification(Certifications $certification): self
    {
        if ($this->certifications->removeElement($certification)) {
            // set the owning side to null (unless already changed)
            if ($certification->getProvider() === $this) {
                $certification->setProvider(null);
            }
        }

        return $this;
    }
}
