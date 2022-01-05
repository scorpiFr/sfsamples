<?php

namespace App\Controller;

use App\Services\MyService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    /**
     * Page d'accueil
     *
     * @Route("/articles", name="articles")
     */
    public function articles(MyService $myService)
    {
        return new Response("Liste des articles - " . $myService->doSomething());
    }

    /**
     * Page d'accès à un article
     *
     * @Route("/article/{articleId<\d+>}", name="read-article", methods={"GET"})
     */
    public function read($articleId)
    {
        // Nous retrouvons la valeur de la variable $articleId à partir de l'URI
        // Par exemple /article/1234 => $articleId = '1234'

        return new Response("Voici le contenu de l'article avec l'ID $articleId ");
    }
}
