<?php
/**
 * User: mrunkel
 * Date: 2019-05-02
 * Time: 15:49
 */

namespace App\Service;


use App\Entity\File;
use App\Entity\Movie;
use App\Entity\Resolution;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\Finder;

class ScanProcessor extends AbstractController
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function scanDir()
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

}
