<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale<%app.supported_locales%>}')] # Ajout de la locale dans le chemin
class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'project_name' => 'ResaLab'
        ]);
    }

    public function about(): Response
    {
        return $this->render('main/about.html.twig');
    }

    public function contact(): Response
    {
        return $this->render('main/contact.html.twig');
    }
}
