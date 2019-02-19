<?php
/**
 * User: mrunkel
 * Date: 2019-02-19
 * Time: 16:25
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ScanController extends AbstractController
{

    /**
     * @Route("/scan")
     * @return Response
     * @throws \Exception
     */
    public function number()
    {
        $number = random_int(0, 100);

        return $this->render('scan/main.html.twig', ['number' => $number]);
    }

}
