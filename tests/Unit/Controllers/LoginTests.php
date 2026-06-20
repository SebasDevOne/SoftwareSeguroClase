<?php
// tests/Unit/Controllers/LoginTests.php
namespace Tests\Unit\Controllers;

use Tests\TestCase;

class LoginTest extends TestCase
{
    // TC-J01
    public function test_validate_con_email_vacio_retorna_false(): void
    {
        $mockUser = $this->createMock(\User::class);
        $mockUser->method('login')->willReturn(false);

        $login = new \Login();
        $result = $login->validate('', '1234', $mockUser);
        $this->assertFalse($result);
    }
}
