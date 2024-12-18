<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Note;
use App\Entity\Room;
use App\Entity\User;
use App\Form\RoomType;
// use Symfony\Component\Routing\Attribute\Route;
use App\Entity\BookReview;
use App\Form\UserTypeForm;
use App\Entity\Availability;
use App\Entity\RoomReservation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    #[Route('/admin/users', name:'admin_users')]
    public function manageUsers(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        return $this->render('admin/manage_users.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/admin/users/ban/{id}', name:'admin_ban_user')]
    public function banUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->ban();
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/users/unban/{id}', name:'admin_unban_user')]
    public function unbanUser(User $user, EntityManagerInterface $entityManager): Response 
    { 
        $user->unban(); 
        $entityManager->persist($user); 
        $entityManager->flush(); 
        return $this->redirectToRoute('admin_users');
    }

    #[Route('/admin/users/role/{id}', name:'admin_edit_role')]
    public function editUserRole(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserTypeForm::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('admin_users');
        }
        return $this->render('admin/edit_role.html.twig', [
            
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/users/delete/{id}', name:'admin_delete_user')]
    public function deleteUser(User $user, EntityManagerInterface $entityManager): Response
    {
        $user->$this->getUser();
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('admin_users');
    }  
    
    #[Route('/admin/rooms/new', name: 'admin_room_new')]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $room = new Room();
    $form = $this->createForm(RoomType::class, $room); 
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($this->getParameter('images_directory'), $newFilename);
            $room->setImage($newFilename);
        }
        $entityManager->persist($room);
        $entityManager->flush();

        return $this->redirectToRoute('admin_rooms');
    }

    return $this->render('admin/room_new.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/admin/rooms', name:'admin_rooms')]
    public function manageRooms(EntityManagerInterface $entityManager): Response
    {
        $rooms = $entityManager->getRepository(Room::class)->findAll();
        return $this->render('admin/manage_rooms.html.twig', [
            'rooms' => $rooms,
        ]);
    }

    #[Route('/admin/rooms/{id}', name: 'admin_room_details')]
public function show($id, EntityManagerInterface $entityManager): Response
{
    $room = $entityManager->getRepository(Room::class)->find($id);
    if (!$room) {
        throw $this->createNotFoundException('La salle n\'existe pas.');
    }
    $reservations = $entityManager->getRepository(RoomReservation::class)->findBy(['room' => $room]);
    $availabilities = $entityManager->getRepository(Availability::class)->findBy(['room' => $room]);
    return $this->render('admin/room_details.html.twig', [
        'room' => $room,
        'reservations' => $reservations,
        'availabilities'=> $availabilities,
    ]);
}


#[Route('/admin/rooms/{id}/edit', name: 'admin_room_edit')]
public function edit($id, Request $request, EntityManagerInterface $entityManager): Response
{
    $room = $entityManager->getRepository(Room::class)->find($id);
    $form = $this->createForm(RoomType::class, $room);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($this->getParameter('images_directory'), $newFilename);
            $room->setImage($newFilename);
        }
       
        $entityManager->flush();

        return $this->redirectToRoute('admin_rooms');
    }

    return $this->render('admin/room_edit.html.twig', [
        'form' => $form->createView(),
        'room' => $room,
    ]);
}

   #[Route('/admin/rooms/{id}/delete', name: 'admin_room_delete')]
public function delete($id, EntityManagerInterface $entityManager): Response
{
    $room = $entityManager->getRepository(Room::class)->find($id);
    if (!$room) { 
        throw $this->createNotFoundException('La salle n\'existe pas.');
    }
    $entityManager->remove($room);
    $entityManager->flush();

    return $this->redirectToRoute('admin_rooms');
}

#[Route('/admin/rooms/{id}/add_unavailable', name: 'admin_add_unavailable', methods: ['POST'])]
public function addUnavailable($id, Request $request, EntityManagerInterface $entityManager): Response
{
    $startStr = $request->request->get('start'); 
    $endStr = $request->request->get('end');
     
    try {
        $start = new \DateTimeImmutable($startStr);
        $end = new \DateTimeImmutable($endStr);
    } catch (\Exception $e) {
        return new Response('Invalid date format', 400);
    }
    $room = $entityManager->getRepository(Room::class)->find($id);
    if (!$room) {
        throw $this->createNotFoundException('La salle n\'existe pas.');
    }

    $availability = new Availability();
    $availability->setRoom($room);
    $availability->setDateReservation($start);
    $availability->setHeureDebut($start);
    $availability->setHeureFin($end);

    $entityManager->persist($availability);
    $entityManager->flush();

   $this->addFlash('success','Unavailable period added successfully.');

    return $this->redirectToRoute('admin_room_details', ['id'=> $id]);
}

#[Route('/admin/rooms/{id}/delete_unavailable', name: 'admin_delete_unavailable', methods: ['POST'])]
public function deleteUnavailable($id, Request $request, EntityManagerInterface $entityManager): Response
{
    $availabilityId = $request->request->get('availabilityId');
    $availability = $entityManager->getRepository(Availability::class)->find($availabilityId);
    
    if (!$availability) {
        return new Response('Unavailable period not found.', 404);
    }

    $entityManager->remove($availability);
    $entityManager->flush();

    $this->addFlash('success','Unavailable period deleted successfully.');

    return $this->redirectToRoute('admin_room_details', ['id'=> $id]);
}

#[Route('admin/books', name: 'admin_books')]
public function bookNotes(EntityManagerInterface $entityManager): Response
{
    $books = $entityManager->getRepository(Book::class)->findAll();
    $bookNotes = [];
   foreach ($books as $book) {
    $notes = $entityManager->getRepository(Note::class)->findBy(['book' => $book]);
    $average = $notes ? array_sum(array_map(fn($note) => $note->getNote(), $notes)) / count($notes) : 0;
    $bookNotes[$book->getId()] = $average;  
           }
    return $this->render('admin/manage_books.html.twig', [
        'books' => $books,
        'bookNotes' => $bookNotes,
       ]);
}

#[Route('/admin/books/{id}/reviews', name: 'admin_book_reviews', methods: ['GET'])]
public function bookReviews($id, EntityManagerInterface $entityManager): Response
{
    $book = $entityManager->getRepository(Book::class)->find($id);
    if (!$book) {
        throw $this->createNotFoundException('Le livre n\'existe pas.');
    }
    $reviews = $entityManager->getRepository(BookReview::class)->findBy(['book' => $book]);
    if (empty($reviews)) {
        $this->addFlash('admin_error', 'Aucune revue pour ce livre.');
    }
    return $this->render('admin/book_reviews.html.twig', [
        'reviews' => $reviews,
        'book' => $book,
    ]);
}

#[Route('/admin/reviews/{id}/delete', name: 'admin_delete_review', methods: ['POST'])]
public function deleteReview($id, EntityManagerInterface $entityManager): Response
{
    $review = $entityManager->getRepository(BookReview::class)->find($id);
    if (!$review) {
        throw $this->createNotFoundException('La revue n\'existe pas.');
    }

    $entityManager->remove($review);
    $entityManager->flush();

    $this->addFlash('admin_success', 'Revue supprimée.');

    return $this->redirectToRoute('admin_book_reviews', ['id' => $review->getBook()->getId()]);
}


}

