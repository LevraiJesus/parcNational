<?php
use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\Trail;

class TrailTest extends TestCase
{
    private $db;
    private $trail;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
        $this->trail = new Trail($this->db);
    }

    public function testCreate()
    {
        $this->trail->name = "Test Trail";
        $this->trail->longitudeStart = "1.234";
        $this->trail->longitudeEnd = "2.345";
        $this->trail->latitudeStart = "3.456";
        $this->trail->latitudeEnd = "4.567";
        $this->trail->distance = 10.5;
        $this->trail->heightDiff = 100;
        $this->trail->pointOfInterest = [1, 2, 3];
        $this->trail->camping = [4, 5];
        $this->trail->difficulty = "medium";
        $this->trail->estimatedTime = "2 hours";
        $this->trail->trailType = "loop";
        $this->trail->seasonAvailability = "all year";

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->db->method('prepare')->willReturn($stmt);

        $result = $this->trail->create();

        $this->assertTrue($result);
    }

    public function testGetTrailsByDifficulty()
    {
        $difficulty = "medium";
        $expectedTrails = [
            ['id' => 1, 'name' => 'Trail 1'],
            ['id' => 2, 'name' => 'Trail 2']
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $stmt->method('fetchAll')->willReturn($expectedTrails);
        $this->db->method('prepare')->willReturn($stmt);

        $result = $this->trail->getTrailsByDifficulty($difficulty);

        $this->assertEquals($expectedTrails, $result);
    }
}
