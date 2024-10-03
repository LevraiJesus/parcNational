<?php

namespace Skand\Backend\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\Camping;
use PDO;

class CampingTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
    }

    public function testCampingCreation()
    {
        $camping = new Camping($this->db);
        $this->assertInstanceOf(Camping::class, $camping);
    }

    public function testCampingAttributes()
    {
        $camping = new Camping($this->db);
        $camping->name = 'Test Camping';
        $camping->description = 'Test Description';
        $camping->location = 'Test Location';
        $camping->price = 100.00;
        $camping->capacity = 50;
        $camping->amenities = 'Test Amenities';

        $this->assertEquals('Test Camping', $camping->name);
        $this->assertEquals('Test Description', $camping->description);
        $this->assertEquals('Test Location', $camping->location);
        $this->assertEquals(100.00, $camping->price);
        $this->assertEquals(50, $camping->capacity);
        $this->assertEquals('Test Amenities', $camping->amenities);
    }

    public function testCampingMethods()
    {
        $camping = new Camping($this->db);
        
        $this->assertTrue(method_exists($camping, 'create'));
        $this->assertTrue(method_exists($camping, 'read'));
        $this->assertTrue(method_exists($camping, 'update'));
        $this->assertTrue(method_exists($camping, 'delete'));
        $this->assertTrue(method_exists($camping, 'getAllCampings'));
    }

    public function testSanitize()
    {
        $camping = new Camping($this->db);
        $camping->name = '<script>alert("XSS")</script>Test Camping';
        $camping->description = '<b>Test Description</b>';

        $reflection = new \ReflectionClass($camping);
        $method = $reflection->getMethod('sanitize');
        $method->setAccessible(true);
        $method->invoke($camping);

        $this->assertEquals('alert(&quot;XSS&quot;)Test Camping', $camping->name);
        $this->assertEquals('Test Description', $camping->description);
    }


}
