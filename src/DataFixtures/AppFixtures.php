<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Book;
use App\Entity\Note;
use App\Entity\Room;
use App\Entity\User;
use App\Entity\BookReview;
use Doctrine\Persistence\ObjectManager;
// use Doctrine\Bundle\FixturesBundle\Fixture;

// class AppFixtures extends Fixture
// {
//     public function load(ObjectManager $manager): void
//     {
//         $faker = Factory::create();
//         $users = [];
//         $books = [];
        
//         for ($i = 0; $i < 10; $i++) {
//             $user = new User();
//             $user->setNom($faker->userName());
//             $user->setPrenom($faker->userName());
//             $user->setPassword($faker->password());
//             $user->setEmail($faker->email());
//             $user->setRoles(['ROLE_USER']);
//             $user->setDateNaissance($faker->dateTimeBetween('-50 years', 'now'));
//             $user->setAdresse('');
//             $user->setCodePostal($faker->phoneNumber());
//             $user->setVille($faker->city());
//             $user->setNumeroTelephone($faker->phoneNumber());
//             $user->setStatut($faker->boolean(0.5));
//             $manager->persist($user);
//             $users[] = $user;
//         }
       
//         for ($i = 0; $i < 50; $i++) {
//             $book = new Book();
//             $book->setTitre($faker->sentence());
//             $book->setAuteur($faker->words(2, true));
//             $book->setAnneePublication($faker->year()); 
//             $book->setResume($faker->text());
//             $book->setImage($faker->imageUrl());
//             $book->setDisponibilite($faker->boolean());
//             $manager->persist($book);
//             $books[] = $book;
//         }

//         for ($i = 0; $i < 5; $i++) {
//             $room = new Room();
//             $room->setNom($faker->words(1, true));
//             $room->setCapacite(mt_rand(5,10));
//             $room->setEquipements($faker->words(3, true));
//             $manager->persist($room);
//         }
        
//         for ($i = 0; $i < 100; $i++) {   
//             $review = new BookReview();
//             $review->setCommentaire($faker->text());
//             $review->setDateCommentaire($faker->dateTime());
//             $review->setBook($faker->randomElement($books));
//             $review->setUser($faker->randomElement($users));
//             $manager->persist($review);
//         } 

//         for ($i = 0; $i < 100; $i++) {
//             $note = new Note();
//             $note->setNote(mt_rand(1,5));
//             $note->setBook($faker->randomElement($books)); 
//             $note->setUser($faker->randomElement($users)); 
//             $manager->persist($note);        
//         }
//         $manager->flush();
//     }
// }
