<?php

use PHPUnit\Framework\TestCase;
use Skand\Backend\Controllers\TrailController;
use Skand\Backend\Models\Trail;

class TrailControllerTest extends TestCase
{
    private $trailController;
    private $dbMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(PDO::class);
        $trailMock = $this->createMock(Trail::class);
        $this->trailController = new TrailController($this->dbMock);
    }

    public function testCreate()
    {
        $_POST = [
            'name' => 'Test Trail',
            'longitudeStart' => '1.234',
            'longitudeEnd' => '2.345',
            'latitudeStart' => '3.456',
            'latitudeEnd' => '4.567',
            'distance' => '10.5',
            'heightDiff' => '100',
            'difficulty' => 'medium'
        ];

        ob_start();
        $this->trailController->create();
        $output = ob_get_clean();

        $this->assertStringContainsString('Trail was created', $output);
    }

    public function testGetByDifficulty()
    {
        $difficulty = 'easy';

        ob_start();
        $this->trailController->getByDifficulty($difficulty);
        $output = ob_get_clean();

        $this->assertJson($output);
    }
}
