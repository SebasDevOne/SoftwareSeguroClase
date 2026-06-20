<?php
// tests/Unit/Controllers/LoginTest.php
namespace Tests\Unit\Controllers;

use Tests\TestCase;

class LoginTest extends TestCase
{
    public function test_login_con_credenciales_vacias_falla(): void
    {
        $login = new \Login();
        $result = $login->validate('', '');
        $this->assertFalse($result);
    }

    public function test_login_con_credenciales_validas_retorna_true(): void
    {
        $login = new \Login();
        $result = $login->validate('admin', '1234');
        $this->assertTrue($result);
    }
}