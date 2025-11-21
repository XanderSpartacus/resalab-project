<?php

namespace App\Controller;

use App\Services\ResourceStatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale<%app.supported_locales%>}')] # Ajout de la locale dans le chemin
class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(ResourceStatsService $resourceStatsService): Response // Injection du service
    {
        $totalResources = $resourceStatsService->getTotalResourcesCount(); // Appel du service
        $resourcesByCategory = $resourceStatsService->getResourcesCountByCategory(); // Appel du service

        return $this->render('main/index.html.twig', [
            'project_name' => 'ResaLab',
            'totalResources' => $totalResources, // Passage des données au template
            'resourcesByCategory' => $resourcesByCategory, // Passage des données au template
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
