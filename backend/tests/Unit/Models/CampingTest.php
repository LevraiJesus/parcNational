<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Camping;

class CampingTest extends TestCase
{
    public function testLatitudeProperty()
    {
        $camping = new Camping();
        $this->assertClassHasAttribute('latitude', Camping::class);
        
        $camping->latitude = 40.7128;
        $this->assertEquals(40.7128, $camping->latitude);
        
        $camping->latitude = -90.0;
        $this->assertEquals(-90.0, $camping->latitude);
        
        $camping->latitude = 90.0;
        $this->assertEquals(90.0, $camping->latitude);
    }

    public function testLatitudeRangeValidation()
    {
        $camping = new Camping();
        
        $this->expectException(\InvalidArgumentException::class);
        $camping->latitude = 91.0;
        
        $this->expectException(\InvalidArgumentException::class);
        $camping->latitude = -91.0;
    }

    public function testLatitudeDataType()
    {
        $camping = new Camping();
        
        $camping->latitude = "40.7128";
        $this->assertIsFloat($camping->latitude);
        $this->assertEquals(40.7128, $camping->latitude);
        
        $camping->latitude = 0;
        $this->assertIsFloat($camping->latitude);
        $this->assertEquals(0.0, $camping->latitude);
    }
}
