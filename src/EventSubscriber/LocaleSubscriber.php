<?php
# https://symfony.com/doc/current/session.html#creating-a-localesubscriber
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Ce subscriber est la version recommandée par la documentation de Symfony.
 * Il gère la locale de manière robuste pour chaque requête.
 */
class LocaleSubscriber implements EventSubscriberInterface
{
    private string $defaultLocale;

    public function __construct(string $defaultLocale = 'fr')
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        // Essaie de voir si la locale a été définie comme paramètre de route `_locale`
        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            // Si aucune locale explicite n'a été définie dans cette requête,
            // on utilise celle de la session, ou la locale par défaut.
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // Doit être enregistré après le LocaleSessionListener par défaut de Symfony
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
        ];
    }
}
