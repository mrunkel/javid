<?php
/**
 * User: mrunkel
 * Date: 2019-02-19
 * Time: 16:25
 */

namespace App\Controller;

use App\Entity\File;
use App\Entity\Resolutions;
use App\Entity\Scan;
use App\Entity\Movie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScanController extends AbstractController
{

    /**
     * @Route("/scan")
     * @return Response
     * @throws \Exception
     */
    public function scan(Request $request)
    {
        $output = [];

        $scan = new Scan('');

        $form = $this->createFormBuilder($scan)
                     ->add('path', TextType::class)
                     ->add('search', SubmitType::class, ['label' => 'Search Path'])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scan = $form->getData();
            if ($scan->getPath() !== '') {
                $output = $this->scanDir($scan);
            }
            // $scan->save();
        }

        return $this->render('scan/main.html.twig', [
            'form'   => $form->createView(),
            'output' => $output,
        ]);

    }

    protected function scanDir(Scan $scan)
    {
        $output = [];
        // '/Volumes/Private/drive support/New'
        $path          = $scan->getPath();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($scan);
        $entityManager->flush();

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
                $file     = new File();
                $entityManager->persist($file);
                $output[] = $line;
                $file->setPath($path);
                $file->addScan($scan);
                $file->setName($baseName);

                if ($resolution) {
                    $resolutions = $this->getDoctrine()->getRepository(Resolutions::class);
                    if (!$resObj = $resolutions->findOneBy(['name' => $resolution])) {
                        $resObj = new Resolutions();
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

                $entityManager->flush();
            }
        }

        return $output;

    }
}
