<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;


//https://symfonycasts.com/screencast/symfony-security/verify-email


/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="email", message="This email is already taken.")
 * @UniqueEntity(fields="username", message="This username is already taken.")
 * @ORM\HasLifecycleCallbacks()
 */
class User implements UserInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true)
     * @Assert\NotBlank(message = "This field is required.")
     * @Assert\Email(message = "Choose a valid email.")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message = "This field is required.")
     */
    private $password;

    /**
     * @var array
     *
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var Datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_login;

    /**
     * @var Datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBanned = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $avatar_path = "default1.png";

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $specialty = "";

    /**
     * @ORM\ManyToMany(targetEntity=Certification::class, fetch="EXTRA_LAZY"))
     */
    private $certifications;

    /**
     * @ORM\ManyToMany(targetEntity=Exam::class, fetch="EXTRA_LAZY"))
     */
    private $exams;

    /**
     * @ORM\OneToMany(targetEntity=eSuggestion::class, mappedBy="createdBy")
     */
    private $suggestions;

    /**
     * @ORM\Column(type="integer")
     */
    private $acceptedSugg = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    public function __construct()
    {
        $this->certifications = new ArrayCollection();
        $this->exams = new ArrayCollection();
        $this->suggestions = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(){
        $this->created_at = new Datetime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }

        return array_unique($roles);
    }

    public function hasRole($role)
    {
        return array_search($role, $this->roles);
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        //
    }

    public function serialize(): string
    {
        return serialize([$this->id, $this->username, $this->password]);
    }

    public function unserialize($serialized): void
    {
        [$this->id, $this->username, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }

    public function getLastLogin()
    {
        return $this->last_login;
    }

    public function setLastLogin($last_login): void
    {
        $this->last_login = $last_login;
    }

    public function getCreatedAt() 
    {
        return $this->created_at;
    }

    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    public function getAvatar() {
        return $this->avatar_path;
    }

    public function getAvatarPath() {
        return "imgs/avatars/". $this->avatar_path;
    }

    public function setAvatarPath($avatar_path): self {
        $this->avatar_path = $avatar_path;
        return $this;
    }

    public function getSpecialty(): ?string
    {
        return $this->specialty;
    }

    public function setSpecialty(string $specialty): self
    {
        $this->specialty = $specialty;

        return $this;
    }

    /**
     * @return Collection|Certification[]
     */
    public function getCertifCollection(): Collection
    {
        return $this->certifications;
    }

    public function isAddedCertif(Certification $certification): bool {
        return $this->certifications->contains($certification);
    }

    public function addCertification(Certification $certification): self
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications[] = $certification;
        }

        return $this;
    }

    public function removeCertification(Certification $certification): self
    {
        $this->certifications->removeElement($certification);

        return $this;
    }
    
    /**
     * @return Collection|Exam[]
     */
    public function getExamCollection(): Collection
    {
        return $this->exams;
    }

    public function isAddedExam(Exam $exam): bool {
        return $this->exams->contains($exam);
    }

    public function addExam(Exam $exam): self
    {
        if (!$this->exams->contains($exam)) {
            $this->exams[] = $exam;
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        $this->exams->removeElement($exam);

        return $this;
    }

 /**
     * @return Collection<int, eSuggestion>
     */
    public function getSuggestions(): Collection
    {
        return $this->suggestions;
    }

    public function addSuggestion(eSuggestion $suggestion): self
    {
        if (!$this->suggestions->contains($suggestion)) {
            $this->suggestions[] = $suggestion;
            $suggestion->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSuggestion(eSuggestion $suggestion): self
    {
        if ($this->suggestions->removeElement($suggestion)) {
            // set the owning side to null (unless already changed)
            if ($suggestion->getCreatedBy() === $this) {
                $suggestion->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getAcceptedSugg(): ?int
    {
        return $this->acceptedSugg;
    }

    public function addAcceptedSugg(): self
    {
        $this->acceptedSugg++;

        return $this;
    }

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}