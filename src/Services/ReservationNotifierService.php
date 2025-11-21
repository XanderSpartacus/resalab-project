<?php

namespace App\Services;

use App\Entity\Reservation;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class ReservationNotifierService
{
    private MailerInterface$mailer;
    private Environment $twig;
    private string $adminEmail;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(MailerInterface $mailer, Environment $twig, string $adminEmail, UrlGeneratorInterface $urlGenerator)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->adminEmail = $adminEmail;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Envoie une notification par e-mail à l'admin pour une nouvelle réservation.
     */
    public function notifyAdminAboutNewReservation(Reservation $reservation): void
    {
        // Générer l'URL absolue pour la page de login
        $loginUrl = $this->urlGenerator->generate('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@resalab.com', 'ResaLab Mail Bot')) // Expéditeur
            ->to($this->adminEmail) // L'admin destinataire
            ->subject('Nouvelle réservation effectuée sur Resalab')
            ->htmlTemplate('emails/new_reservation_notification.html.twig') // Template Twig pour le contenu
            ->context([
                // Les variables passées ici seront accessibles dans le template
                'reservation' => $reservation,
                'login_url' => $loginUrl, // On lui passe l'URL absolue'
            ]);

        $this->mailer->send($email); // C'est cette ligne qui envoie le mail
    }
}
