<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FileRepository")
 */
class File
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $path;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     */
    private $size;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Movie", inversedBy="file")
     */
    private $movie;

    /**
     * @ORM\Column(type="boolean")
     */
    private $subs;

    /**
     * @ORM\Column(type="boolean")
     */
    private $uncensored;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Resolution", inversedBy="files")
     */
    private $resolution;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Scan", mappedBy="found")
     */
    private $scans;

    public function __construct()
    {
        $this->scans = new ArrayCollection();
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

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    public function getSubs(): ?bool
    {
        return $this->subs;
    }

    public function setSubs(bool $subs): self
    {
        $this->subs = $subs;

        return $this;
    }

    public function getUncensored(): ?bool
    {
        return $this->uncensored;
    }

    public function setUncensored(bool $uncensored): self
    {
        $this->uncensored = $uncensored;

        return $this;
    }

    public function getResolution(): ?Resolution
    {
        return $this->resolution;
    }

    public function setResolution(?Resolution $resolution): self
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * @return Collection|Scan[]
     */
    public function getScans(): Collection
    {
        return $this->scans;
    }

    public function addScan(Scan $scan): self
    {
        if (!$this->scans->contains($scan)) {
            $this->scans[] = $scan;
            $scan->addFound($this);
        }

        return $this;
    }

    public function removeScan(Scan $scan): self
    {
        if ($this->scans->contains($scan)) {
            $this->scans->removeElement($scan);
            $scan->removeFound($this);
        }

        return $this;
    }
}
