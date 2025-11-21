<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Ce contrôleur gère la redirection de la racine '/' vers la version localisée de la page d'accueil
 * C'est une bonne pratique pour les applications multi-langues, ou toutes les routes sont préfixées par une locale
 */
class DefaultRedirectController extends AbstractController
{
    #[Route('/', name: 'app_redirect_to_default_locale')]
    public function index():Response
    {
        // Redirige vers la page d'accueil avec la locale par défaut (fr)
        return $this->redirectToRoute('app_main', ['_locale' => 'fr']); # on peut changer ['_locale' => 'en']
    }
}
