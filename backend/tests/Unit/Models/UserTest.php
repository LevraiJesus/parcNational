<?php

namespace Skand\Backend\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Skand\Backend\Models\User;
use PDO;

class UserTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = $this->createMock(PDO::class);
    }

    public function testUserCreation()
    {
        $user = new User($this->db);
        $this->assertInstanceOf(User::class, $user);
    }

    public function testNameProperty()
    {
        $user = new User($this->db);
        
        $user->name = 'John Doe';
        $this->assertEquals('John Doe', $user->name);
        
        $user->name = '';
        $this->assertEmpty($user->name);
        
        $user->name = null;
        $this->assertNull($user->name);
        
        $user->name = 123;
        $this->assertEquals(123, $user->name);
    }
    

    public function testSanitizeMethod()
    {
        $user = new User($this->db);
        $user->name = '<script>alert("XSS")</script>John Doe';
        $user->email = 'john@example.com';
        $user->password = 'password123';

        $reflection = new \ReflectionClass($user);
        $method = $reflection->getMethod('sanitize');
        $method->setAccessible(true);
        $method->invoke($user);

        $this->assertEquals('alert(&quot;XSS&quot;)John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(password_verify('password123', $user->password));
    }
}
