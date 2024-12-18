<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\RoomReservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(Security $security, EntityManagerInterface $entityManager): Response
    { $rooms = $entityManager->getRepository(Room::class)->findAll();
        $user = $security->getUser();
        if (!$user || in_array('ROLE_BANNED', $user->getRoles())) {
            return $this->redirectToRoute('app_login', [
                'message' => 'Vous devez être connecté pour accéder à cette fonctionnalité.'
            ]);
        }
        return $this->render('reservation/index.html.twig', [
            'rooms' => $rooms,
        ]);

       
    }

    #[Route('/reservation/book/{roomId}', name: 'app_reservation_room')]
    public function reserver(Request $request, $roomId, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();

        if (!$user || in_array('ROLE_BANNED', $user->getRoles())) {
            return $this->redirectToRoute('app_login');
        }

        $activeSubscription = $user->getActiveSubscription();
        if (!$activeSubscription) {
            return $this->redirectToRoute('app_subscribe', [
                'message' => 'Vous devez avoir un abonnement valide pour accéder à cette fonctionnalité.'
            ]);
        }

        $room = $entityManager->getRepository(Room::class)->find($roomId);

        if (!$room) {
            throw $this->createNotFoundException('La salle n\'existe pas.');
        }

        $reservations = $entityManager->getRepository(RoomReservation::class)
                                     ->findBy(['room' => $room]);

        return $this->render('reservation/book.html.twig', [
            'room' => $room,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/reservation/confirm/{roomId}', name: 'app_reservation_confirm')]
    public function confirm(Request $request, $roomId, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();

        if (!$user || in_array('ROLE_BANNED', $user->getRoles())) {
            return $this->redirectToRoute('app_login');
        }

        $activeSubscription = $user->getActiveSubscription();
        if (!$activeSubscription) {
            return $this->redirectToRoute('app_subscribe', [
                'message' => 'Vous devez avoir un abonnement valide pour accéder à cette fonctionnalité.'
            ]);
        }

        $room = $entityManager->getRepository(Room::class)->find($roomId);

        if (!$room) {
            throw $this->createNotFoundException('La salle n\'existe pas.');
        }

        $startStr = $request->query->get('start');
        $endStr = $request->query->get('end');

        $startStr = substr($startStr, 0, 19);
        $endStr = substr($endStr, 0, 19);

        try {
            $start = new \DateTimeImmutable($startStr);
            $end = new \DateTimeImmutable($endStr);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Invalid date format.');
        }

        $reservations = $entityManager->getRepository(RoomReservation::class)
                                      ->findBy(['room' => $room]);

        return $this->render('reservation/book.html.twig', [
            'room' => $room,
            'start' => $start,
            'end' => $end,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/reservation/create/{roomId}', name: 'app_reservation_create')]
    public function create(Request $request, $roomId, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();
    
        if (!$user || in_array('ROLE_BANNED', $user->getRoles())) {
            return $this->redirectToRoute('app_login');
        }
    
        $activeSubscription = $user->getActiveSubscription();
        if (!$activeSubscription) {
            $this->addFlash('user_error', 'Vous devez avoir un abonnement valide pour accéder à cette fonctionnalité.');
            return $this->redirectToRoute('app_subscribe');
        }
    
        $room = $entityManager->getRepository(Room::class)->find($roomId);
    
        if (!$room) {
            throw $this->createNotFoundException('La salle n\'existe pas.');
        }
    
        $startStr = $request->query->get('start');
        $endStr = $request->query->get('end');
    
        $startStr = substr($startStr, 0, 19);
        $endStr = substr($endStr, 0, 19);
    
        try {
            $start = new \DateTimeImmutable($startStr);
            $end = new \DateTimeImmutable($endStr);
        } catch (\Exception $e) {
            $this->addFlash('user_error', 'Format de date invalide.');
            return $this->redirectToRoute('app_reservation_room', ['roomId' => $roomId]);
        }
    
        // Vérification de la durée de la réservation
        $interval = $start->diff($end);
        $hours = $interval->h + ($interval->days * 24);
    
        if ($hours < 1 || $hours > 4) {
            $this->addFlash('user_error', 'La réservation doit être entre 1 heure et 4 heures.');
            return $this->redirectToRoute('app_reservation_room', ['roomId' => $roomId]);
        } else {
    
        // Vérification du chevauchement des réservations existantes
        $existingReservations = $entityManager->getRepository(RoomReservation::class)
                                              ->createQueryBuilder('r')
                                              ->where('r.room = :room')
                                              ->andWhere('r.date_reservation = :date')
                                              ->andWhere('r.heure_debut < :end')
                                              ->andWhere('r.heure_fin > :start')
                                              ->setParameter('room', $room)
                                              ->setParameter('date', $start->format('Y-m-d'))
                                              ->setParameter('start', $start)
                                              ->setParameter('end', $end)
                                              ->getQuery()
                                              ->getResult();
    
        if (count($existingReservations) > 0) {
            $this->addFlash('user_error', 'Il y a déjà une réservation pour cette période.');
            return $this->redirectToRoute('app_reservation_room', ['roomId' => $roomId]);
        } else {
    
        $reservation = new RoomReservation();
        $reservation->setRoom($room);
        $reservation->setUser($user);
        $reservation->setDateReservation($start);
        $reservation->setHeureDebut($start);
        $reservation->setHeureFin($end);
    
        $entityManager->persist($reservation);
        $entityManager->flush();
    
        $this->addFlash('user_success', 'La réservation a été effectuée avec succès.');
        return $this->redirectToRoute('app_reservation_room', ['roomId' => $roomId]);
    }
}
}
    #[Route('/reservation/delete/{reservationId}', name: 'app_reservation_delete')]
    public function delete($reservationId, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = $security->getUser();

        if (!$user || in_array('ROLE_BANNED', $user->getRoles())) {
            return $this->redirectToRoute('app_login');
        }

        $reservation = $entityManager->getRepository(RoomReservation::class)->find($reservationId);

        if (!$reservation) {
            throw $this->createNotFoundException('La réservation n\'existe pas.');
        }

        // Vérification que l'utilisateur est le propriétaire de la réservation
        if ($reservation->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette réservation.');
        }

        $entityManager->remove($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation supprimée avec succès.');

        return $this->redirectToRoute('app_reservation', ['roomId' => $reservation->getRoom()->getId()]);
    }
}

