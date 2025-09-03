<?php

namespace App\Entity;

use App\Repository\CertificationsRepository;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CertificationsRepository::class)
 * @UniqueEntity(fields="title", message="This title is already taken.")
 * @ORM\HasLifecycleCallbacks
 */
class Certification
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description = "";

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $thumbnail_path = "placeholder.png";

    /**
     * @ORM\OneToMany(targetEntity=Exam::class, mappedBy="certification", orphanRemoval=false, fetch="EXTRA_LAZY")
     */
    private $exams;

    /**
     * @ORM\ManyToOne(targetEntity=eProvider::class, inversedBy="certifications")
     */
    private $eProvider;

    public function __construct()
    {
        $this->exams = new ArrayCollection();
    }

    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getThumbnailName() {
        return $this->thumbnail_path;
    }

    public function getThumbnailPath() {
        return "/imgs/thumbnails/certifications/".$this->thumbnail_path;
    }

    public function setThumbnailPath($thumbnail_path): self {
        $this->thumbnail_path = $thumbnail_path;
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
            $exam->setCertification($this);
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        if ($this->exams->removeElement($exam)) {
            // set the owning side to null (unless already changed)

            if ($exam->getCertification() === $this) {
                $exam->setCertification(null);
            }
        }

        return $this;
    }

    public function getCountQ()
    {   
        $questionCount = 0;

        foreach ($this->exams as $exam) {
            $questionCount +=  $exam->getCountQ();
        }

        return $questionCount;
    }

    public function setEProvider(?eProvider $eProvider): self
    {
        $this->eProvider = $eProvider;

        return $this;
    }

    public function getEProvider(): ?eProvider
    {
        return $this->eProvider;
    }
}
