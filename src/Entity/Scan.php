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

    /**
     * @param Scan $scan
     *
     * @return array
     */

    protected function scanDir()
    {
        $path          = $this->getPath();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($this);

        $validExtensions = ['*.mp4', '*.mkv', '*.avi', '*.wmv', '*.rmvb', '*.m4v', '*.flv'];

        $fileFinder = new Finder();
        $fileFinder->files()->in($path)->name($validExtensions);
        foreach ($fileFinder as $origName) {

            // skip hidden files
            if (substr($origName, 0, 1) === '.') {
                // $output[] = $fileName . ' not processed, hidden file';
                continue;
            }

            $baseName = pathinfo($origName, PATHINFO_BASENAME);

            $fileName = pathinfo($origName, PATHINFO_FILENAME);

            $fileName = strtolower($fileName);

            // pull out junk
            $junklist = [
                'tttt',
                'jav.guru',
                'jav,guru',
                'jav guru',
                '(',
                ')',
                '+',
                '[',
                ']',
                'full1',
                'full',
                'sss',
                'xxx',
                'better_',
            ];

            $fileName = str_replace($junklist, '', $fileName);

            $resolution = '';
            // extract resolution codes (480p, 720p, etc)
            if (preg_match('/(240p|480p|720p|1080p|1440p|2160p|[^(?:DA)]SD[^(?:DE)]|HD[^(?:TA)]|FHD|lores|small)/i',
                $fileName, $output_array)) {
                $resolution = $output_array[1];
                $fileName   = str_replace($resolution, '', $fileName);
            }

            $english = '';
            // extract eng tags
            if (preg_match('/(eng|English|Eng Sub)/i', $fileName, $output_array)) {
                $english  = $output_array[1];
                $fileName = str_replace($english, '', $fileName);
            }

            $unc = '';
            // extract unc tags
            if (preg_match('/(\sunc\s|Uncensored)/i', $fileName, $output_array)) {
                $unc      = $output_array[1];
                $fileName = str_replace($unc, '', $fileName);
            }

            $code = 'none';
            // extract the code
            if (preg_match('/([a-z]{2,5})[-,_]?(\d{2,5})/i', $fileName, $output_array)) {
                $code = strtoupper($output_array[1]) . '-' . $output_array[2];
                // detect part A/B  after the code is sometimes a, b, c, d, e etc.
                $fileName = str_replace($output_array[0], '', $fileName);
                // strip the code out
            }
            $line             = new \stdClass();
            $line->origName   = $baseName;
            $line->code       = $code;
            $line->resolution = $resolution;
            $line->subs       = strlen($english) > 0;
            $line->unc        = strlen($unc) > 0;
            $line->rest       = trim($fileName);

            $files = $this->getDoctrine()->getRepository(File::class);

            if (!$files->findOneBy(['name' => $baseName, 'path' => $path])) {
                $file = new File();
                $entityManager->persist($file);
                $output[] = $line;
                $file->setPath($path);
                $file->addScan($this);
                $file->setName($baseName);

                if ($resolution) {
                    $resolution = $this->getDoctrine()->getRepository(Resolution::class);
                    if (!$resObj = $resolution->findOneBy(['name' => $resolution])) {
                        $resObj = new Resolution();
                        $resObj->setName($resolution);
                        $entityManager->persist($resObj);
                    }
                    $file->setResolution($resObj);
                }
                if ($code !== 'none') {
                    $movies = $this->getDoctrine()->getRepository((Movie::class));
                    if (!$movie = $movies->findOneBy(['code' => $code])) {
                        $movie = new Movie();
                        $movie->setCode($code);
                        $entityManager->persist($movie);
                    }
                    $file->setMovie($movie);
                }
                try {
                    $file->setSize(filesize($origName));
                } catch (\ErrorException $e) {
                    echo 'FileSize Exception on: ' . $origName;
                }
                $file->setSubs($line->subs);
                $file->setUncensored($line->unc);
                $this->addFound($file);
                $entityManager->flush();
            }
        }

        $this->setCompleted(true);
        $this->setScannedAt(new \DateTime());
        $entityManager->flush();

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
