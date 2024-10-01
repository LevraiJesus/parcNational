<?php
use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\Booking;

class BookingTest extends TestCase
{
    private $db;
    private $booking;

    protected function setUp(): void
    {
        // Set up a mock database connection
        $this->db = $this->createMock(PDO::class);
        $this->booking = new Booking($this->db);
    }

    public function testCreate()
    {
        // Set up test data
        $this->booking->camping_id = 1;
        $this->booking->user_id = 1;
        $this->booking->start_date = '2023-07-01';
        $this->booking->end_date = '2023-07-05';
        $this->booking->status = 'pending';

        // Mock the database interaction
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->db->method('prepare')->willReturn($stmt);

        // Perform the test
        $result = $this->booking->create();

        // Assert the result
        $this->assertTrue($result);
    }

    public function testCheckAvailability()
    {
        // Set up test data
        $camping_id = 1;
        $start_date = '2023-07-01';
        $end_date = '2023-07-05';

        // Mock the database interaction
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn(['count' => 0]);
        $this->db->method('prepare')->willReturn($stmt);

        // Perform the test
        $result = $this->booking->checkAvailability($camping_id, $start_date, $end_date);

        // Assert the result
        $this->assertTrue($result);
    }
}
