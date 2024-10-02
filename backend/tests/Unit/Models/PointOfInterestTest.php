<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\PointOfInterest;

class PointOfInterestTest extends TestCase
{
    public function testNamePropertyExists()
    {
        $poi = new PointOfInterest();
        $this->assertObjectHasAttribute('name', $poi);
    }

    public function testNamePropertyIsPublic()
    {
        $poi = new PointOfInterest();
        $reflection = new \ReflectionProperty(PointOfInterest::class, 'name');
        $this->assertTrue($reflection->isPublic());
    }

    public function testNamePropertyCanBeSet()
    {
        $poi = new PointOfInterest();
        $poi->name = 'Test POI';
        $this->assertEquals('Test POI', $poi->name);
    }

    public function testNamePropertyCanBeNull()
    {
        $poi = new PointOfInterest();
        $poi->name = null;
        $this->assertNull($poi->name);
    }

    public function testNamePropertyAcceptsString()
    {
        $poi = new PointOfInterest();
        $poi->name = 'String Test';
        $this->assertIsString($poi->name);
    }
}
