<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Trail;

class TrailTest extends TestCase
{
    public function testPointOfInterestAttribute()
    {
        $trail = new Trail();
        $this->assertNull($trail->pointOfInterest);

        $poi = 'Scenic Viewpoint';
        $trail->pointOfInterest = $poi;
        $this->assertEquals($poi, $trail->pointOfInterest);
    }

    public function testPointOfInterestCanBeEmpty()
    {
        $trail = new Trail();
        $trail->pointOfInterest = '';
        $this->assertEmpty($trail->pointOfInterest);
    }

    public function testPointOfInterestCanBeArray()
    {
        $trail = new Trail();
        $pois = ['Waterfall', 'Cave', 'Historic Site'];
        $trail->pointOfInterest = $pois;
        $this->assertIsArray($trail->pointOfInterest);
        $this->assertCount(3, $trail->pointOfInterest);
        $this->assertEquals($pois, $trail->pointOfInterest);
    }
}
