<?php

namespace Skand\Backend\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\PointOfInterest;
use PDO;

class PointOfInterestTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
    }

    public function testPointOfInterestCreation()
    {
        $poi = new PointOfInterest($this->db);
        $this->assertInstanceOf(PointOfInterest::class, $poi);
    }

    public function testPointOfInterestAttributes()
    {
        $poi = new PointOfInterest($this->db);
        $poi->name = 'Test POI';
        $poi->type = 'Test Type';
        $poi->latitude = 40.7128;
        $poi->longitude = -74.0060;
        $poi->description = 'Test Description';

        $this->assertEquals('Test POI', $poi->name);
        $this->assertEquals('Test Type', $poi->type);
        $this->assertEquals(40.7128, $poi->latitude);
        $this->assertEquals(-74.0060, $poi->longitude);
        $this->assertEquals('Test Description', $poi->description);
    }

    public function testPointOfInterestMethods()
    {
        $poi = new PointOfInterest($this->db);
        
        $this->assertTrue(method_exists($poi, 'create'));
        $this->assertTrue(method_exists($poi, 'read'));
        $this->assertTrue(method_exists($poi, 'update'));
        $this->assertTrue(method_exists($poi, 'delete'));
        $this->assertTrue(method_exists($poi, 'getAllPointsOfInterest'));
    }

    public function testSanitize()
    {
        $poi = new PointOfInterest($this->db);
        $poi->name = '<script>alert("XSS")</script>Test POI';
        $poi->description = '<b>Test Description</b>';

        $reflection = new \ReflectionClass($poi);
        $method = $reflection->getMethod('sanitize');
        $method->setAccessible(true);
        $method->invoke($poi);

        $this->assertEquals('alert(&quot;XSS&quot;)Test POI', $poi->name);
        $this->assertEquals('Test Description', $poi->description);
    }

}
