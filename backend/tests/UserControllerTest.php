<?php

use PHPUnit\Framework\TestCase;
use Skand\Backend\Controllers\UserController;
use Skand\Backend\Models\User;

class UserControllerTest extends TestCase
{
    private $userController;
    private $dbMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(PDO::class);
        $userMock = $this->createMock(User::class);
        $this->userController = new UserController($this->dbMock);
    }

    public function testCreate()
    {
        $_POST = [
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Test User',
            'firstname' => 'Test',
            'phoneNumber' => '1234567890'
        ];

        ob_start();
        $this->userController->create();
        $output = ob_get_clean();

        $this->assertStringContainsString('User was created', $output);
    }

    public function testLogin()
    {
        $_POST = [
            'email' => 'test@example.com',
            'password' => 'password123'
        ];

        ob_start();
        $this->userController->login();
        $output = ob_get_clean();

        $this->assertStringContainsString('Login successful', $output);
    }
}
