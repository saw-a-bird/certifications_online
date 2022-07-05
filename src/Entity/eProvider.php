<?php

namespace App\Entity;

use App\Repository\eProvidersRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=eProvidersRepository::class)
 * @UniqueEntity(fields="name", message="This name is already used.")
 */
class eProvider
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
    private $thumbnail_path = "placeholder.png";

    /**
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Exam::class, mappedBy="eProvider", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $exams;

    /**
     * @ORM\OneToMany(targetEntity=Certification::class, mappedBy="eProvider", fetch="EXTRA_LAZY")
     */
    private $certifications;

    public function __construct() {
        $this->exams = new ArrayCollection();
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
     * @return Collection|Exam[]
     */
    public function getExams(): Collection
    {
        return $this->exams;
    }

    public function addExam(Exam $exam): self
    {
        if (!$this->exams->contains($exam)) {
            $this->exams[] = $exam;
            $exam->setEProvider($this);
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        if ($this->exams->removeElement($exam)) {
            // set the owning side to null (unless already changed)
            if ($exam->getEProvider() === $this) {
                $exam->setEProvider(null);
            }
        }

        return $this;
    }

    public function getThumbnailName() {
        return $this->thumbnail_path;
    }

    public function getThumbnailPath() {
        return "/imgs/thumbnails/providers/".$this->thumbnail_path;
    }

    public function setThumbnailPath($thumbnail_path): self {
        $this->thumbnail_path = $thumbnail_path;
        return $this;
    }

    /**
     * @return Collection<int, Certification>
     */
    public function getCertifications(): Collection
    {
        return $this->certifications;
    }

    public function addCertification(Certification $certification): self
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications[] = $certification;
            $certification->setEProvider($this);
        }

        return $this;
    }

    public function removeCertification(Certification $certification): self
    {
        if ($this->certifications->removeElement($certification)) {
            // set the owning side to null (unless already changed)
            if ($certification->getEProvider() === $this) {
                $certification->setEProvider(null);
            }
        }

        return $this;
    }
}
