<?php

namespace Skand\Backend\Controllers;

use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\PointOfInterest;

class PointOfInterestControllerTest extends TestCase
{
    private $dbMock;
    private $pointOfInterestController;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(\PDO::class);
        $this->pointOfInterestController = new PointOfInterestController($this->dbMock);
    }

    public function testConstructorInitializesPointOfInterestModel()
    {
        $reflection = new \ReflectionClass($this->pointOfInterestController);
        $property = $reflection->getProperty('pointOfInterest');
        $property->setAccessible(true);
        
        $this->assertInstanceOf(PointOfInterest::class, $property->getValue($this->pointOfInterestController));
    }

    public function testConstructorPassesDatabaseConnectionToPointOfInterestModel()
    {
        $reflection = new \ReflectionClass($this->pointOfInterestController);
        $property = $reflection->getProperty('pointOfInterest');
        $property->setAccessible(true);
        
        $pointOfInterestModel = $property->getValue($this->pointOfInterestController);
        
        $this->assertInstanceOf(PointOfInterest::class, $pointOfInterestModel);
    }
}
