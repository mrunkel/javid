<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Movie;
use App\Entity\Resolution;
use App\Entity\Scan;
use App\Form\ScanType;
use App\Repository\ScanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/scan")
 */
class ScanController extends AbstractController
{

    /**
     * @Route("/add")
     * @return Response
     * @throws \Exception
     */
    public function add(Request $request)
    {
        $messages = [];
        $errors   = [];

        $scan  = new Scan('');
        $scans = $this->getDoctrine()->getRepository(Scan::class);

        $form = $this->createFormBuilder($scan)
                     ->add('path', TextType::class)
                     ->add('search', SubmitType::class, ['label' => 'Search Path'])
                     ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $scan = $form->getData();
            if ($scan->getPath() !== '') {
                // check if valid
                if (is_dir($scan->getPath())) {
                    // did we already add this?
                    if ($scans->findOneBy(['path' => $scan->getPath()])) {
                        $errors[] = 'Path already exists';
                    } else {
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($scan);
                        $entityManager->flush();
                        $messages[] = 'Path added successfully.';
                    }
                } else {
                    $errors[] = 'Invalid Path';
                }
            }
        }

        return $this->render('scan/add.html.twig', [
            'form'     => $form->createView(),
            'messages' => $messages,
            'errors'   => $errors,
        ]);
    }


    /**
     * @Route("/", name="scan_index", methods={"GET"})
     */
    public function index(ScanRepository $scanRepository): Response
    {
        return $this->render('scan/index.html.twig', [
            'scans' => $scanRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="scan_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $scan = new Scan();
        $form = $this->createForm(ScanType::class, $scan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($scan);
            $entityManager->flush();

            return $this->redirectToRoute('scan_index');
        }

        return $this->render('scan/new.html.twig', [
            'scan' => $scan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="scan_show", methods={"GET"})
     */
    public function show(Scan $scan): Response
    {
        return $this->render('scan/show.html.twig', [
            'scan' => $scan,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="scan_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Scan $scan): Response
    {
        $form = $this->createForm(ScanType::class, $scan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('scan_index', [
                'id' => $scan->getId(),
            ]);
        }

        return $this->render('scan/edit.html.twig', [
            'scan' => $scan,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="scan_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Scan $scan): Response
    {
        if ($this->isCsrfTokenValid('delete'.$scan->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($scan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('scan_index');
    }
}
