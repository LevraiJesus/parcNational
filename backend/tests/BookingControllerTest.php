<?php

use PHPUnit\Framework\TestCase;
use Skand\Backend\Controllers\BookingController;
use Skand\Backend\Models\Booking;

class BookingControllerTest extends TestCase
{
    private $bookingController;
    private $dbMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(PDO::class);
        $bookingMock = $this->createMock(Booking::class);
        $this->bookingController = new BookingController($this->dbMock);
    }

    public function testCreate()
    {
        // Mock the input data
        $inputData = json_encode([
            'camping_id' => 1,
            'user_id' => 1,
            'start_date' => '2023-01-01',
            'end_date' => '2023-01-05'
        ]);

        // Set up expectations for the mock
        $this->dbMock->expects($this->once())
            ->method('prepare')
            ->willReturn($this->createMock(\PDOStatement::class));

        // Capture the output
        ob_start();
        $this->bookingController->create();
        $output = ob_get_clean();

        // Assert the response
        $this->assertStringContainsString('Booking was created', $output);
    }

}
