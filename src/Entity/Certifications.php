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
class Certifications
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Providers::class, inversedBy="certifications")
     * @ORM\JoinColumn(nullable=true)
     */
    private $provider;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $thumbnail_path = "placeholder.png";

    /**
     * @ORM\Column(type="datetime")
     */
    private $creation_date;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $update_date;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="certifications")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=Exams::class, mappedBy="certification", orphanRemoval=true)
     */
    private $exams;

    /**
     * @ORM\OneToMany(targetEntity=Comments::class, mappedBy="writtenOn", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=CertificationRates::class, mappedBy="certification", orphanRemoval=true)
     */
    private $certificationRates;

    /**
     * @ORM\Column(type="integer")
     */
    private $countQ = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBlocked = false;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="creations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $createdBy;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->exams = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->certificationRates = new ArrayCollection();
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

    public function getProvider(): ?Providers
    {
        return $this->provider;
    }

    public function setProvider(?Providers $provider): self {
        $this->provider = $provider;
        return $this;
    }

    public function getThumbnailName() {
        return $this->thumbnail_path;
    }

    public function getThumbnailPath() {
        return "/imgs/thumbnails/".$this->thumbnail_path;
    }

    public function setThumbnailPath($thumbnail_path): self {
        $this->thumbnail_path = $thumbnail_path;
        return $this;
    }

    public function getCreationDate()
    {
        return $this->creation_date;
    }

    public function setCreationDate($creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getUpdateDate() {
        return $this->update_date;
    }

    public function setUpdateDate($update_date): self {
        $this->update_date = $update_date;
        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection|Exams[]
     */
    public function getExams(): Collection
    {
        return $this->exams;
    }

    public function addExam(Exams $exam): self
    {
        if (!$this->exams->contains($exam)) {
            $this->exams[] = $exam;
            $exam->setCertification($this);
        }

        return $this;
    }

    public function removeExam(Exams $exam): self
    {
        if ($this->exams->removeElement($exam)) {
            // set the owning side to null (unless already changed)

            if ($exam->getCertification() === $this) {
                $exam->setCertification(null);
                $this->decCountQ(count($exam->getQuestions()));
            }
        }

        return $this;
    }

    /**
     * Gets triggered only on insert
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->creation_date = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->update_date = new \DateTime("now");
    }

    /**
     * @return Collection|Comments[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setWrittenOn($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getWrittenOn() === $this) {
                $comment->setWrittenOn(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CertificationRates[]
     */
    public function getCertificationRates(): Collection
    {
        return $this->certificationRates;
    }

    public function addCertificationRate(CertificationRates $certificationRate): self
    {
        if (!$this->certificationRates->contains($certificationRate)) {
            $this->certificationRates[] = $certificationRate;
            $certificationRate->setCertification($this);
        }

        return $this;
    }

    public function removeCertificationRate(CertificationRates $certificationRate): self
    {
        if ($this->certificationRates->removeElement($certificationRate)) {
            // set the owning side to null (unless already changed)
            if ($certificationRate->getCertification() === $this) {
                $certificationRate->setCertification(null);
            }
        }

        return $this;
    }

    public function getCountQ(): ?int
    {
        return $this->countQ;
    }

    public function setCountQ(int $countQ): self
    {
        $this->countQ = $countQ;

        return $this;
    }

    public function addCountQ(int $increase): self
    {
        $this->countQ+=$increase;

        return $this;
    }

    public function decCountQ(int $decrease): self
    {
        $this->countQ-=$decrease;

        return $this;
    }

    public function getIsBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
