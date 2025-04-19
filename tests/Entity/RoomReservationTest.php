<?php
use App\Entity\RoomReservation;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
class RoomReservationTest extends TestCase
{
    public function testAddAndRemoveRoomReservation()
{
    $user = new User();
    $reservation = new RoomReservation();

    $user->addRoomReservation($reservation);
    $this->assertTrue($user->getRoomReservations()->contains($reservation));

    $user->removeRoomReservation($reservation);
    $this->assertFalse($user->getRoomReservations()->contains($reservation));
}
}

