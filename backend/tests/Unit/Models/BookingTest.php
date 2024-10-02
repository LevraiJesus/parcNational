<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Booking;

class BookingTest extends TestCase
{
    public function testBookingCreation()
    {
        $booking = new Booking();
        $this->assertInstanceOf(Booking::class, $booking);
    }

    public function testBookingAttributes()
    {
        $booking = new Booking([
            'user_id' => 1,
            'service_id' => 2,
            'booking_date' => '2023-05-01',
            'status' => 'pending'
        ]);

        $this->assertEquals(1, $booking->user_id);
        $this->assertEquals(2, $booking->service_id);
        $this->assertEquals('2023-05-01', $booking->booking_date);
        $this->assertEquals('pending', $booking->status);
    }

    public function testBookingRelationships()
    {
        $booking = new Booking();
        
        $this->assertTrue(method_exists($booking, 'user'));
        $this->assertTrue(method_exists($booking, 'service'));
    }

    public function testBookingStatusEnum()
    {
        $booking = new Booking();
        $this->assertIsArray($booking->getStatusOptions());
        $this->assertContains('pending', $booking->getStatusOptions());
        $this->assertContains('confirmed', $booking->getStatusOptions());
        $this->assertContains('cancelled', $booking->getStatusOptions());
    }

    public function testBookingValidation()
    {
        $booking = new Booking();
        $this->assertTrue(method_exists($booking, 'getValidationRules'));
        $rules = $booking->getValidationRules();
        
        $this->assertArrayHasKey('user_id', $rules);
        $this->assertArrayHasKey('service_id', $rules);
        $this->assertArrayHasKey('booking_date', $rules);
        $this->assertArrayHasKey('status', $rules);
    }
}
