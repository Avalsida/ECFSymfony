<?php

namespace App\Tests\Entity;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSetAndGetEmail()
    {
        $user = new User();
        $user->setEmail('test@example.com');

        $this->assertSame('test@example.com', $user->getEmail());
    }

    public function testSetAndGetRoles()
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);

        $this->assertTrue($user->isAdmin());
    }
    public function testBanAndUnban()
{
    $user = new User();
    $this->assertFalse($user->isBanned());

    $user->ban();
    $this->assertTrue($user->isBanned());

    $user->unban();
    $this->assertFalse($user->isBanned());
}
}
