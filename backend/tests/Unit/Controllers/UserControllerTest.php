<?php

namespace Skand\Backend\Controllers;

use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\User;
use Skand\Backend\helpers\FileUploadHelper;

class UserControllerTest extends TestCase
{
    private $dbMock;
    private $userController;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(\PDO::class);
        $this->userController = new UserController($this->dbMock);
    }

    public function testConstructorInitializesUserModel()
    {
        $reflection = new \ReflectionClass($this->userController);
        $property = $reflection->getProperty('user');
        $property->setAccessible(true);
        
        $this->assertInstanceOf(User::class, $property->getValue($this->userController));
    }

    public function testCreateUserSuccess()
    {
        $inputData = json_encode([
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Test User',
            'firstname' => 'Test',
            'phoneNumber' => '1234567890',
            'admin' => false
        ]);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
            ->method('create')
            ->willReturn(true);

        $reflection = new \ReflectionClass($this->userController);
        $property = $reflection->getProperty('user');
        $property->setAccessible(true);
        $property->setValue($this->userController, $userMock);

        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->expectOutputString(json_encode(["message" => "CONTROLLER : User was created."]));

        $this->userController->create();

        $this->assertEquals(201, http_response_code());
    }

    public function testCreateUserFailure()
    {
        $inputData = json_encode([
            'email' => 'test@example.com',
            'password' => 'password123',
            'name' => 'Test User',
            'firstname' => 'Test',
            'phoneNumber' => '1234567890',
            'admin' => false
        ]);

        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
            ->method('create')
            ->willReturn(false);

        $reflection = new \ReflectionClass($this->userController);
        $property = $reflection->getProperty('user');
        $property->setAccessible(true);
        $property->setValue($this->userController, $userMock);

        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $this->expectOutputString(json_encode(["message" => "CONTROLLER : Unable to create user."]));

        $this->userController->create();

        $this->assertEquals(503, http_response_code());
    }

    public function testLoginSuccess()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['CONTENT_TYPE'] = 'application/json';

        $inputData = json_encode([
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);

        file_put_contents('php://input', $inputData);
    
        // Mock the User class
        $userMock = $this->createMock(User::class);
        $userMock->expects($this->once())
            ->method('getUserByEmail')
            ->with('test@example.com')
            ->willReturn([
                'id' => 1,
                'email' => 'test@example.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'name' => 'Test User',
                'firstname' => 'Test',
                'admin' => false
            ]);
    
        $userMock->expects($this->once())
            ->method('generateJWT')
            ->willReturn('mocked_jwt_token');
    
        // Set the mocked User object in the controller
        $reflection = new \ReflectionClass($this->userController);
        $property = $reflection->getProperty('user');
        $property->setAccessible(true);
        $property->setValue($this->userController, $userMock);
    
        // Capture the output
        ob_start();
        $this->userController->login();
        $output = ob_get_clean();
    
        // Assert the output
        $this->assertEquals(
            json_encode(["message" => "Login successful", "token" => "mocked_jwt_token"]),
            $output,
            "Login output doesn't match expected value"
        );
    
        // Assert the status code
        $this->assertEquals(200, http_response_code(), "HTTP status code is not 200");
    }
    





    // Additional tests for other methods can be added here
}
