<?php
use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\User;

class UserTest extends TestCase
{
    private $db;
    private $user;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
        $this->user = new User($this->db);
    }

    public function testCreate()
    {
        $this->user->email = "test@example.com";
        $this->user->password = "password123";
        $this->user->name = "Test";
        $this->user->firstname = "User";
        $this->user->phoneNumber = "1234567890";
        $this->user->admin = false;

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true);
        $this->db->method('prepare')->willReturn($stmt);

        $result = $this->user->create();

        $this->assertTrue($result);
    }

    public function testGenerateJWT()
    {
        $this->user->id = 1;
        $this->user->email = "test@example.com";
        $this->user->name = "Test";
        $this->user->firstname = "User";
        $this->user->admin = false;

        $_ENV['JWT_SECRET'] = 'test_secret';

        $jwt = $this->user->generateJWT();

        $this->assertNotEmpty($jwt);
        $this->assertIsString($jwt);
    }
}
