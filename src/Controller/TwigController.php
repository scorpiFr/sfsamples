<?php
// src/Controller/TwigController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TwigController extends AbstractController
{
    /**
     * Page d'accueil
     *
     * @Route("/twig", name="twig_test")
     */
    public function test()
    {
        $data = ['name' => 'Camille'];
        $html = $this->get('twig')->render('twig/test.html.twig', $data);
        return new Response($html);
    }
}

