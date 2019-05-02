<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="datetime")
     */
    private $scannedAt;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\File", inversedBy="scans")
     */
    private $found;

    public function __construct($path)
    {
        $this->found     = new ArrayCollection();
        $this->scannedAt = new \DateTime("now");
        $this->path      = $path;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function addFound(File $found): self
    {
        if (!$this->found->contains($found)) {
            $this->found[] = $found;
        }

        return $this;
    }

    public function removeFound(File $found): self
    {
        if ($this->found->contains($found)) {
            $this->found->removeElement($found);
        }

        return $this;
    }
}
