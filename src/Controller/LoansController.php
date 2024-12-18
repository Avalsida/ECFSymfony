<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\BookLoan;

class LoansController extends AbstractController
{
    #[Route('/profile/loans', name: 'user_book_loans')]
    public function viewBookLoans(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir cette page.');
        }

        $bookLoans = $user->getBookLoans();

        return $this->render('profile/book_loans.html.twig', [
            'user' => $user,
            'bookLoans' => $bookLoans,
        ]);
    }

    #[Route('/profile/room_reservation', name: 'user_room_reservations')]
    public function viewRoomReservations(): Response
    {
        $user = $this->getUser();
        if (!$user || in_array('ROLE_BANNED', $user->getRoles())) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir cette page.');
        }

        $roomReservations = $user->getRoomReservations();

        return $this->render('profile/room_reservations.html.twig', [
            'user' => $user,
            'roomReservations' => $roomReservations,
        ]);
    }
}
