<?php

namespace App\Security\Voter;

use App\Entity\Reservation;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class ReservationVoter extends Voter
{
    // On définit les constantes pour les actions que notre Voter saura gérer
    // Dans notre cas, on aura 3 actions : EDIT, VIEW et DELETE
    // Vous pouvez ajouter d'autres actions selon vos besoins
    // C'est une bonne pratique pour éviter les fautes de frappe'
    public const CREATE = 'RESERVATION_CREATE';
    public const EDIT = 'RESERVATION_EDIT';
    public const VIEW = 'RESERVATION_VIEW';
    public const DELETE = 'RESERVATION_DELETE';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /*
     * Détermine si ce Voter doit s'activer pour l'action (attribute) et l'objet (subject) donnés.
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        // Le voter gère la permission de création (sans objet)
        if($attribute === self::CREATE) {
            return true;
        }

        // Et les permissions sur des objets Reservation existants
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof Reservation;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Règle n°1 : L'admin à tous les droits on ne va pas plus loin
        if($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $user = $token->getUser();

        // Règle n°2 : Pour toute action l'utilisateur doit être connecté
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Règle n°3 : Pour toute action l'utilisateur doit être connecté
        if($attribute === self::CREATE) {
            return true;
        }

        /** @var $reservation */
        $reservation = $subject;

        // Règle n°4 : Logique fine pour un utilisateur connecté sur une de ses réservations
        switch ($attribute) {
            case self::VIEW:
                // Il peut voir sa réservation (si il est propriétaire) quelque soit son statut
                return $reservation->getReservedBy() === $user;

            CASE self::EDIT:
            CASE self::DELETE:
                // /** C'EST LA RÈGLE LA PLUS IMPORTANTE **/
                // Il peut modifier/supprimer sa réservation SEULEMENT SI
                // 1. Il est en le propriétaire et
                // 2. Le statut de la réservation est "pending"
                return $reservation->getReservedBy() === $user && $reservation->getStatus() === 'pending';
        }

        return false;
    }
}
