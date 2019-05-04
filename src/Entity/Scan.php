<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Finder\Finder;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScanRepository")
 */
class Scan
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $path;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $scannedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\File", inversedBy="scans")
     */
    private $found;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $completed;

    public function __construct($path)
    {
        $this->found     = new ArrayCollection();
        $this->createdAt = new \DateTime("now");
        $this->path      = $path;
        $this->completed = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScannedAt(): ?\DateTimeInterface
    {
        return $this->scannedAt;
    }

    public function setScannedAt(\DateTimeInterface $scannedAt): self
    {
        $this->scannedAt = $scannedAt;

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFound(): Collection
    {
        return $this->found;
    }

    public function removeFound(File $found): self
    {
        if ($this->found->contains($found)) {
            $this->found->removeElement($found);
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCompleted(): ?bool
    {
        return $this->completed;
    }

    public function setCompleted(bool $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function addFound(File $found): self
    {
        if (!$this->found->contains($found)) {
            $this->found[] = $found;
        }

        return $this;
    }
}
