<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FooterController extends AbstractController
{
    /**
     * @Route("/footer", name="footer")
     */
    public function index(): Response
    {
        $aboutUs = "hello";
        return $this->render('layouts/footer.html.twig', [
            'about_us' => $aboutUs,
        ]);
    }
}
