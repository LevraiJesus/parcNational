<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Skand\Backend\Models;

class UserTest extends TestCase
{
    public function testNameProperty()
    {
        $user = new User();
        $this->assertClassHasAttribute('name', User::class);
        
        $user->name = 'John Doe';
        $this->assertEquals('John Doe', $user->name);
        
        $user->name = '';
        $this->assertEmpty($user->name);
        
        $user->name = null;
        $this->assertNull($user->name);
        
        $user->name = 123;
        $this->assertIsString($user->name);
        $this->assertEquals('123', $user->name);
    }
}
