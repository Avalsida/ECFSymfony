<?php

namespace App\Controller;
use App\Entity\Book;
use App\Entity\Note;
use App\Entity\BookReview;
use App\Form\BookReviewType;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BookController extends AbstractController

{

   #[Route('/books', name: 'app_books')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $books = $entityManager->getRepository(Book::class)->findAll();
        $bookNotes = [];

        foreach ($books as $book) {
            $notes = $entityManager->getRepository(Note::class)->findBy(['book' => $book]);
            $average = $notes ? array_sum(array_map(fn($note) => $note->getNote(), $notes)) / count($notes) : 0;
            $bookNotes[$book->getId()] = $average;
        }
        return $this->render('books/index.html.twig', [
            'books' => $books,
            'bookNotes' => $bookNotes,
        ]);
    }

    #[Route('/books/{id}/rate', name: 'rate_book', methods: ['POST'])]
    public function rate($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $book = $entityManager->getRepository(Book::class)->find($id);

        if (!$book || !isset($data['rating'])) {
            return $this->json(['success' => false, 'message' => 'Invalid request'], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->getUser()) {
            return $this->json(['success' => false, 'message' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $user = $this->getUser();
        $existingNote = $entityManager->getRepository(Note::class)->findOneBy(['book' => $book, 'user' => $user]);

        if ($existingNote) {
            $existingNote->setNote($data['rating']);
            $entityManager->flush();
            return $this->json(['success' => true, 'message' => 'Your rating has been updated.']);
        } else {
            $note = new Note();
            $note->setBook($book);
            $note->setUser($user);
            $note->setNote($data['rating']);
            $entityManager->persist($note);
            $entityManager->flush();
            return $this->json(['success' => true, 'message' => 'Thank you for your rating!']);
        }
    }

    #[Route('/books/{id}/', name: 'book_details', methods: ['GET', 'POST'])]
    public function show($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->find($id);
        $user = $this->getUser();
        $note = null;
        $commentForm = null;

        if ($user) {
            $note = $entityManager->getRepository(Note::class)->findOneBy(['book' => $book, 'user' => $user]);
        }

        $review = new BookReview();
        $form = $this->createForm(BookReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $review->setBook($book);
            $review->setUser($user);
            $review->setDateCommentaire(new \DateTime());
            $entityManager->persist($review);
            $entityManager->flush();
            $this->addFlash('success', 'Votre commentaire a été ajouté!');
            return $this->redirectToRoute('book_details', ['id' => $id]);
        }

        $comments = $entityManager->getRepository(BookReview::class)->findBy(['book' => $book]);

        return $this->render('books/book_details.html.twig', [
            'book' => $book,
            'note' => $note,
            'form' => $form->createView(),
            'comments' => $comments,
        ]);
    }
}






