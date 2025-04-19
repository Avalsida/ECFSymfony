<?php
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EmailUniqueTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testEmailIsUnique()
    {
        $user1 = new User();
        $user1->setNom('Dupont');  // Ajout d'une valeur obligatoire
        $user1->setPrenom('Jean');  
        $user1->setDateNaissance(new \DateTime('1990-01-01'));
        $user1->setEmail('unique@example.com');
        $user1->setPassword('password123');  
        $user1->setAdresse('10 rue des tests');
        $user1->setCodePostal('75001');
        $user1->setVille('Paris');
        $user1->setNumeroTelephone('0601020304');

        $this->entityManager->persist($user1);
        $this->entityManager->flush();

        $user2 = new User();
        $user2->setNom('Martin');
        $user2->setPrenom('Paul');
        $user2->setDateNaissance(new \DateTime('1995-06-15'));
        $user2->setEmail('unique@example.com');
        $user2->setPassword('password456');  
        $user2->setAdresse('20 avenue Symfony');
        $user2->setCodePostal('69001');
        $user2->setVille('Lyon');
        $user2->setNumeroTelephone('0612345678');

        $this->expectException(\Doctrine\DBAL\Exception\UniqueConstraintViolationException::class);
        $this->entityManager->persist($user2);
        $this->entityManager->flush();
    }
}
