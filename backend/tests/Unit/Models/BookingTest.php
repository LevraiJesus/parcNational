<?php

namespace Skand\Backend\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\Booking;
use PDOStatement;
use PDO;

class BookingTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
    }

    public function testBookingCreation()
    {
        $booking = new Booking($this->db);
        $this->assertInstanceOf(Booking::class, $booking);
    }

    public function testBookingAttributes()
    {
        $booking = new Booking($this->db);
        $booking->camping_id = 1;
        $booking->user_id = 2;
        $booking->start_date = '2023-05-01';
        $booking->end_date = '2023-05-05';
        $booking->status = 'pending';

        $this->assertEquals(1, $booking->camping_id);
        $this->assertEquals(2, $booking->user_id);
        $this->assertEquals('2023-05-01', $booking->start_date);
        $this->assertEquals('2023-05-05', $booking->end_date);
        $this->assertEquals('pending', $booking->status);
    }

    public function testBookingMethods()
    {
        $booking = new Booking($this->db);
        
        $this->assertTrue(method_exists($booking, 'create'));
        $this->assertTrue(method_exists($booking, 'read'));
        $this->assertTrue(method_exists($booking, 'update'));
        $this->assertTrue(method_exists($booking, 'delete'));
        $this->assertTrue(method_exists($booking, 'getAllBookings'));
        $this->assertTrue(method_exists($booking, 'checkAvailability'));
    }

    public function testCheckAvailability()
    {
        $booking = new Booking($this->db);
        
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetch')->willReturn(['count' => 0]);
        
        $this->db->method('prepare')->willReturn($stmt);

        $result = $booking->checkAvailability(1, '2023-05-01', '2023-05-05');
        $this->assertTrue($result);
    }
}
