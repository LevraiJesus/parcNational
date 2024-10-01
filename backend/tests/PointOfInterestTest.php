<?php
use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\PointOfInterest;

class PointOfInterestTest extends TestCase
{
    private $db;
    private $poi;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
        $this->poi = new PointOfInterest($this->db);
    }

    public function testCreate()
    {
        $this->poi->name = "Test POI";
        $this->poi->type = "landmark";
        $this->poi->latitude = "1.234";
        $this->poi->longitude = "5.678";
        $this->poi->description = "A test point of interest";

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->db->method('prepare')->willReturn($stmt);

        $result = $this->poi->create();

        $this->assertTrue($result);
    }

    public function testGetAllPointsOfInterest()
    {
        $expectedPOIs = [
            ['id' => 1, 'name' => 'POI 1'],
            ['id' => 2, 'name' => 'POI 2']
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchAll')->willReturn($expectedPOIs);
        $this->db->method('prepare')->willReturn($stmt);

        $result = $this->poi->getAllPointsOfInterest();

        $this->assertEquals($expectedPOIs, $result);
    }
}
