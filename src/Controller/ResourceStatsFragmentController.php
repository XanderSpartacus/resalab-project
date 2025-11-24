<?php

namespace App\Controller;

use App\Service\ResourceStatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ResourceStatsFragmentController extends AbstractController
{
    /**
     * Cette action est destinée à être incluse via ESI pour afficher les statistiques des ressources.
     * Elle a sa propre route et peut être mise en cache indépendamment.
     */
    #[Route('/_resource_stats/{_locale}', name: 'app_resource_stats_fragment', methods: ['GET'])]
    public function _resourceStatsAction(
        ResourceStatsService $resourceStatsService,
        Request $request,
        string $_locale // Récupère la locale depuis l'URL
    ): Response {
        // Définir la locale pour cette sous-requête si nécessaire (normalement déjà fait par LocaleSubscriber)
        $request->setLocale($_locale);

        $totalResources = $resourceStatsService->getTotalResourcesCount();
        $resourcesByCategory = $resourceStatsService->getResourcesCountByCategory();

        $response = $this->render('main/_resource_stats.html.twig', [
            'totalResources' => $totalResources,
            'resourcesByCategory' => $resourcesByCategory,
        ]);

        // Mettre en cache ce fragment pour 1 heure (par exemple)
        $response->setSharedMaxAge(3600);
        $response->setPublic();

        return $response;
    }
}
