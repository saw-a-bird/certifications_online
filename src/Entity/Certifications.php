<?php

namespace App\Entity;

use App\Repository\CertificationsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CertificationsRepository::class)
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
     * @ORM\Column(type="string", length=50)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
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
     * @ORM\OneToMany(targetEntity=Enrolled::class, mappedBy="certification")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @return Collection|Enrolled[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(Enrolled $enrolled): self {
        if (!$this->users->contains($enrolled)) {
            $this->users[] = $enrolled;
            $enrolled->setCertification($this);
        }

        return $this;
    }

    public function removeUser(Enrolled $enrolled): self
    {
        if ($this->users->removeElement($enrolled)) {
            // set the owning side to null (unless already changed)
            if ($enrolled->getCertification() === $this) {
                $enrolled->setCertification(null);
            }
        }

        return $this;
    }
}
