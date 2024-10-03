<?php

namespace Skand\Backend\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\Trail;
use PDO;

class TrailTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
    }

    public function testTrailCreation()
    {
        $trail = new Trail($this->db);
        $this->assertInstanceOf(Trail::class, $trail);
    }

    public function testPointOfInterestAttribute()
    {
        $trail = new Trail($this->db);
        $this->assertNull($trail->pointOfInterest);

        $poi = 'Scenic Viewpoint';
        $trail->pointOfInterest = $poi;
        $this->assertEquals($poi, $trail->pointOfInterest);
    }

    public function testPointOfInterestCanBeArray()
    {
        $trail = new Trail($this->db);
        $pois = [1, 2, 3];
        $trail->pointOfInterest = $pois;
        $this->assertIsArray($trail->pointOfInterest);
        $this->assertCount(3, $trail->pointOfInterest);
        $this->assertEquals($pois, $trail->pointOfInterest);
    }

    public function testSanitizeMethod()
    {
        $trail = new Trail($this->db);
        $trail->name = '<script>alert("XSS")</script>Test Trail';
        $trail->pointOfInterest = [1, 2, '3'];
        $trail->longitudeStart = '180.5';
        $trail->latitudeStart = '-91';
    
        $reflection = new \ReflectionClass($trail);
        $method = $reflection->getMethod('sanitize');
        $method->setAccessible(true);
        $method->invoke($trail);
    
        $this->assertEquals('alert(&quot;XSS&quot;)Test Trail', $trail->name);
        $this->assertEquals([1, 2, 3], $trail->pointOfInterest);
        $this->assertEquals(180.5, $trail->longitudeStart);
        $this->assertEquals(-91.0, $trail->latitudeStart);
    }
    
    
    
}
