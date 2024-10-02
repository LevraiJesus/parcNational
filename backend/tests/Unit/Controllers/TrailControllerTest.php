<?php

namespace Skand\Backend\Controllers;

use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\Trail;
use Skand\Backend\helpers\FileUploadHelper;

class TrailControllerTest extends TestCase
{
    private $dbMock;
    private $trailController;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(\PDO::class);
        $this->trailController = new TrailController($this->dbMock);
    }

    public function testConstructorInitializesTrailModel()
    {
        $reflection = new \ReflectionClass($this->trailController);
        $property = $reflection->getProperty('trail');
        $property->setAccessible(true);
        
        $this->assertInstanceOf(Trail::class, $property->getValue($this->trailController));
    }

    public function testCreateTrailSuccess()
    {
        $inputData = json_encode([
            'name' => 'Test Trail',
            'longitudeStart' => 1.23,
            'longitudeEnd' => 4.56,
            'latitudeStart' => 7.89,
            'latitudeEnd' => 10.11,
            'distance' => 5.5,
            'heightDiff' => 100,
            'pointOfInterest' => 'Test POI',
            'camping' => true,
            'difficulty' => 'Medium'
        ]);

        $trailMock = $this->createMock(Trail::class);
        $trailMock->expects($this->once())
            ->method('create')
            ->willReturn(true);

        $reflection = new \ReflectionClass($this->trailController);
        $property = $reflection->getProperty('trail');
        $property->setAccessible(true);
        $property->setValue($this->trailController, $trailMock);

        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->expectOutputString(json_encode(["message" => "Trail was created.", "id" => null]));

        $this->trailController->create();

        $this->assertEquals(201, http_response_code());
    }

    public function testCreateTrailFailure()
    {
        $inputData = json_encode([
            'name' => 'Test Trail',
            'longitudeStart' => 1.23,
            'longitudeEnd' => 4.56,
            'latitudeStart' => 7.89,
            'latitudeEnd' => 10.11,
            'distance' => 5.5,
            'heightDiff' => 100,
            'pointOfInterest' => 'Test POI',
            'camping' => true,
            'difficulty' => 'Medium'
        ]);

        $trailMock = $this->createMock(Trail::class);
        $trailMock->expects($this->once())
            ->method('create')
            ->willReturn(false);

        $reflection = new \ReflectionClass($this->trailController);
        $property = $reflection->getProperty('trail');
        $property->setAccessible(true);
        $property->setValue($this->trailController, $trailMock);

        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->expectOutputString(json_encode(["message" => "Unable to create trail."]));

        $this->trailController->create();

        $this->assertEquals(503, http_response_code());
    }

    // Additional tests for other methods can be added here
}
