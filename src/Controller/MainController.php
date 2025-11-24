<?php

namespace App\Controller;

use App\Service\ResourceStatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/{_locale<%app.supported_locales%>}')] # Ajout de la locale dans le chemin
class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    // AVANT : public function index(ResourceStatsService $resourceStatsService): Response
    public function index(): Response // Injection du service
    {
        // AVANT : $totalResources = $resourceStatsService->getTotalResourcesCount(); // Appel du service
        // AVANT : $resourcesByCategory = $resourceStatsService->getResourcesCountByCategory(); // Appel du service

        return $this->render('main/index.html.twig', [
            'project_name' => 'ResaLab',
            // AVANT : 'totalResources' => $totalResources, // Passage des données au template
            // AVANT : 'resourcesByCategory' => $resourcesByCategory, // Passage des données au template
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
