<?php

namespace Skand\Backend\Controllers;

use PHPUnit\Framework\TestCase;
use Skand\Backend\Controllers;
use Skand\Backend\Models\Booking;

class BookingControllerTest extends TestCase
{
    private $bookingController;

    protected function setUp(): void
    {
        parent::setUp();
        $mockDb = $this->createMock(\PDO::class);
        $this->bookingController = new BookingController($mockDb);
    }

    public function testBookingPropertyIsInitiallySet()
    {
        $reflection = new \ReflectionClass($this->bookingController);
        $property = $reflection->getProperty('booking');
        $property->setAccessible(true);
        
        $this->assertInstanceOf(Booking::class, $property->getValue($this->bookingController));
    }

    public function testBookingPropertyCanBeSet()
    {
        $mockBooking = $this->createMock(Booking::class);
       
        $reflection = new \ReflectionClass($this->bookingController);
        $property = $reflection->getProperty('booking');
        $property->setAccessible(true);
       
        $property->setValue($this->bookingController, $mockBooking);
       
        $this->assertSame($mockBooking, $property->getValue($this->bookingController));
    }
}
