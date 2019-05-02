<?php

namespace App\Controller;

use App\Entity\Resolution;
use App\Form\ResolutionType;
use App\Repository\ResolutionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/resolution")
 */
class ResolutionController extends AbstractController
{
    /**
     * @Route("/", name="resolution_index", methods={"GET"})
     */
    public function index(ResolutionRepository $resolutionRepository): Response
    {
        return $this->render('resolution/index.html.twig', [
            'resolutions' => $resolutionRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="resolution_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $resolution = new Resolution();
        $form = $this->createForm(ResolutionType::class, $resolution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resolution);
            $entityManager->flush();

            return $this->redirectToRoute('resolution_index');
        }

        return $this->render('resolution/new.html.twig', [
            'resolution' => $resolution,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resolution_show", methods={"GET"})
     */
    public function show(Resolution $resolution): Response
    {
        return $this->render('resolution/show.html.twig', [
            'resolution' => $resolution,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="resolution_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Resolution $resolution): Response
    {
        $form = $this->createForm(ResolutionType::class, $resolution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('resolution_index', [
                'id' => $resolution->getId(),
            ]);
        }

        return $this->render('resolution/edit.html.twig', [
            'resolution' => $resolution,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="resolution_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Resolution $resolution): Response
    {
        if ($this->isCsrfTokenValid('delete'.$resolution->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($resolution);
            $entityManager->flush();
        }

        return $this->redirectToRoute('resolution_index');
    }
}
