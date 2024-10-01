<?php

use PHPUnit\Framework\TestCase;
use Skand\Backend\Controllers\CampingController;
use Skand\Backend\Models\Camping;

class CampingControllerTest extends TestCase
{
    private $campingController;
    private $dbMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(PDO::class);
        $campingMock = $this->createMock(Camping::class);
        $this->campingController = new CampingController($this->dbMock);
    }

    public function testCreate()
    {
        $_POST = [
            'name' => 'Test Camping',
            'longitude' => '1.234',
            'latitude' => '5.678',
            'description' => 'A test camping site',
            'price' => '50.00',
            'capacity' => '100'
        ];

        ob_start();
        $this->campingController->create();
        $output = ob_get_clean();

        $this->assertStringContainsString('Camping was created', $output);
    }

    public function testRead()
    {
        $campingId = 1;

        ob_start();
        $this->campingController->read($campingId);
        $output = ob_get_clean();

        $this->assertJson($output);
    }
}
