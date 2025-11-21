<?php

namespace App\Security\Voter;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Ce Voter a pour but de gérer les permissions complexes sur l'entité Resource
 * Il permet de contraliser la logique de sécurité pour déterminer qui a le droit
 * de voir, créer, modifier ou supprimer une ressource.
 */
final class ResourceVoter extends Voter
{
    public const CREATE = 'RESOURCE_CREATE'; // Permission globale pour créer une ressource
    public const EDIT = 'RESOURCE_EDIT'; // Permission globale pour modifier une ressource
    public const VIEW = 'RESOURCE_VIEW'; // Permission globale pour voir une ressource
    public const DELETE = 'RESOURCE_DELETE'; // Permission globale pour supprimer une ressource

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Cette méthode est le gardien du Voter
     * Elle est appelée en premier pour déterminer si ce Voter doit être utilisé
     * pour la permission (attribute) et l'objet (subject) demandé
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // https://symfony.com/doc/current/security/voters.html
        // Cas 1 : Le Voter doit s'activer pour la permsission de création (qui n'a pas d'objet)'
        if($attribute === self::CREATE) {
            return true;
        }

        // Cas 2 : Pour toutes les autres permissions, le Voter doit s'activer
        // que si l'objet est une instance de notre entité resource
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\Resource;
    }

    /**
     * C'est ici que toute la logique de permission est implémentée
     * Cette méthode n'est appelée que si supports() a retourné true
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Règle n°1 : Tout le monde (même anonyme) peut voir une ressource
        // ON traite ce cas en premier car c'est la permission la plus ouverte
        if($attribute === self::VIEW) {
            return true;
        }

        // Pour toutes les autres actions (CREATE, EDIT, DELETE) l'utilisateur doit être connecté
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Règle n°2 : Si l'utilisateur est connecté, on vérifie si il est admmin
        // L'admin a tout les droits restants (CREATE, EDIT, DELETE)
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        // Règle n°3 : L'utilisateur est connecté mais pas admin
        // Il aura e faire aucune des actions restantes
        return false;
    }
}
