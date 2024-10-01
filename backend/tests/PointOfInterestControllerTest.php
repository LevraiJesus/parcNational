<?php

use PHPUnit\Framework\TestCase;
use Skand\Backend\Controllers\PointOfInterestController;
use Skand\Backend\Models\PointOfInterest;

class PointOfInterestControllerTest extends TestCase
{
    private $poiController;
    private $dbMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(PDO::class);
        $poiMock = $this->createMock(PointOfInterest::class);
        $this->poiController = new PointOfInterestController($this->dbMock);
    }

    public function testCreate()
    {
        $_POST = [
            'name' => 'Test POI',
            'type' => 'landmark',
            'latitude' => '1.234',
            'longitude' => '5.678',
            'description' => 'A test point of interest'
        ];

        ob_start();
        $this->poiController->create();
        $output = ob_get_clean();

        $this->assertStringContainsString('Point of Interest was created', $output);
    }

    public function testIndex()
    {
        ob_start();
        $this->poiController->index();
        $output = ob_get_clean();

        $this->assertJson($output);
    }
}
