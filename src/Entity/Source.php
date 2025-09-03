<?php

namespace App\Entity;

use App\Repository\SourceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SourceRepository::class)
 */
class Source
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="string")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filePath;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $domain;
    
    public function __construct($id, $filePath, $domain)
    {
        $this->id = $id;
        $this->domain = $domain;
        $this->filePath = $filePath;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(): ?string
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }
}
