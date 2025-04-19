<?php

namespace App\Tests\Routes;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class RoutesTest extends WebTestCase
{
    public function testProtectedRoute()
    {
        $client = static::createClient();

        // Récupère le gestionnaire d'entités
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        // Récupère l'utilisateur de test
        $user = $entityManager->getRepository(User::class)->findOneByEmail('considine.dianna@gmail.com');
       
        // Simule la connexion
        $client->loginUser($user);

        // Teste une route protégée
        $client->request('GET', '/fr/profile');
        $this->assertResponseIsSuccessful();
    }

    public static function listeUrls(): array
    {
        return [
            ['/'],
            ['/books'],
            ['/books/1'],
            ['/books/1/rate'],
            ['/rooms'],
            ['/rooms/1'],
            ['/profile'],
            ['/profile/loans'],
            ['/profile/room_reservation'],
            ['/login'],
            ['/logout'],
            ['/register'],
            ['/reservation'],
            ['/reservation/book/1'],
            ['/reservation/create/1'],
            ['/reservation/delete/1'],
            ['/reservation/confirm/1'],
            ['/subscribe'],
            ['/admin'],
            ['/admin/rooms'],
            ['/admin/rooms/new'],
            ['/admin/rooms/1/edit'],
            ['/admin/users'],
            ['/admin/users/role/1'],
            ['/admin/users/ban/1'],
            ['/admin/users/unban/1'],
            ['/admin/users/delete/1'],
        ];
    }
}
