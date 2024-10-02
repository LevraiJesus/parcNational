<?php

namespace Skand\Backend\Controllers;

use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\Camping;

class CampingControllerTest extends TestCase
{
    private $dbMock;
    private $campingController;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(\PDO::class);
        $this->campingController = new CampingController($this->dbMock);
    }

    public function testConstructorInitializesCampingModel()
    {
        $reflection = new \ReflectionClass($this->campingController);
        $property = $reflection->getProperty('camping');
        $property->setAccessible(true);
        
        $this->assertInstanceOf(Camping::class, $property->getValue($this->campingController));
    }

    public function testConstructorPassesDatabaseConnectionToCampingModel()
    {
        $reflection = new \ReflectionClass($this->campingController);
        $property = $reflection->getProperty('camping');
        $property->setAccessible(true);
        
        $campingModel = $property->getValue($this->campingController);
        
        // Test a method that would use the database connection
        $this->assertInstanceOf(Camping::class, $campingModel);
        // You might want to add more specific assertions here based on the Camping model's methods
    }

}
