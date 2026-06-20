<?php
// tests/Unit/Controllers/LoginTests.php
namespace Tests\Unit\Controllers;

use Tests\TestCase;

class LoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . '/../../../controllers/Login.php';
    }

    // TC-J01
    public function test_validate_con_email_vacio_retorna_false(): void
    {
        $mockUser = $this->createMock(\User::class);
        $mockUser->method('login')->willReturn(false);

        $login = new \Login();
        $result = $login->validate('', '1234', $mockUser);
        $this->assertFalse($result);
    }

    // TC-J02
    public function test_validate_con_pass_vacia_retorna_false(): void
    {
        $mockUser = $this->createMock(\User::class);
        $mockUser->method('login')->willReturn(false);

        $login = new \Login();
        $result = $login->validate('admin@test.com', '', $mockUser);
        $this->assertFalse($result);
    }

    // TC-J03
    public function test_validate_con_credenciales_inexistentes_retorna_false(): void
    {
        $mockUser = $this->createMock(\User::class);
        $mockUser->method('login')->willReturn(false);

        $login = new \Login();
        $result = $login->validate('noexiste@prueba.com', 'clave_falsa', $mockUser);
        $this->assertFalse($result);
    }
}
