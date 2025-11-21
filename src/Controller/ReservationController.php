<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Security\Voter\ReservationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/{_locale<%app.supported_locales%>}/reservation')] // Route de base modifiée
class ReservationController extends AbstractController
{
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        $reservations = [];
        if($this->isGranted('ROLE_ADMIN')){
            $reservations = $reservationRepository->findAll();
        } else {
            $reservations = $reservationRepository->findBy(['reservedBy' => $this->getUser()]);
        }

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // On ajoute cette ligne au début de la méthode
        $this->denyAccessUnlessGranted(ReservationVoter::CREATE);

        $reservation = new Reservation();
        $isAdmin = $this->isGranted('ROLE_ADMIN');

        // Utilisation de ReservationType personnalisé
        $form = $this->createForm(ReservationType::class, $reservation, [
            'is_admin' => $isAdmin,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'utilisateur n'est pas admin, on force le propriétaire à être l'utilisateur connecté
            if(!$isAdmin){
                $reservation->setReservedBy($this->getUser());
            }

            $reservation->setCreatedAt(new \DateTimeImmutable()); // Initialisation de createdAt
            $reservation->setUpdatedAt(new \DateTimeImmutable()); // Initialisation de updatedAt
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été créée avec succès.');

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        $this->denyAccessUnlessGranted(ReservationVoter::VIEW, $reservation);

        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(ReservationVoter::EDIT, $reservation);

        $isAdmin = $this->isGranted('ROLE_ADMIN');
        $form = $this->createForm(ReservationType::class, $reservation, [
            'is_admin' => $isAdmin,
        ] );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setUpdatedAt(new \DateTimeImmutable()); // Mise à jour de updatedAt
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été modifiée avec succès.');

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(ReservationVoter::DELETE, $reservation);

        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'La réservation a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
