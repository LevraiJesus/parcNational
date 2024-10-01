<?php
use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\Camping;

class CampingTest extends TestCase
{
    private $db;
    private $camping;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
        $this->camping = new Camping($this->db);
    }

    public function testCreate()
    {
        $this->camping->name = "Test Camping";
        $this->camping->longitude = "1.234";
        $this->camping->latitude = "5.678";
        $this->camping->description = "A test camping site";
        $this->camping->price = "50.00";
        $this->camping->capacity = 100;
        $this->camping->closeTrails = [1, 2, 3];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->db->method('prepare')->willReturn($stmt);

        $result = $this->camping->create();

        $this->assertTrue($result);
    }

    public function testRead()
    {
        $id = 1;
        $expectedData = [
            'id' => 1,
            'name' => 'Test Camping',
            'longitude' => '1.234',
            'latitude' => '5.678',
            'description' => 'A test camping site',
            'image' => 'test.jpg',
            'price' => '50.00',
            'capacity' => 100,
            'closeTrails' => '1,2,3',
            'manager' => 'John Doe',
            'created_at' => '2023-07-01 00:00:00',
            'modified_at' => '2023-07-01 00:00:00'
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetch')->willReturn($expectedData);
        $this->db->method('prepare')->willReturn($stmt);

        $result = $this->camping->read($id);

        $this->assertTrue($result);
        $this->assertEquals($expectedData['name'], $this->camping->name);
        $this->assertEquals([1, 2, 3], $this->camping->closeTrails);
    }
}
