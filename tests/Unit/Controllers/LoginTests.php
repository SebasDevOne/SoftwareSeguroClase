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
        // Si la BD no está disponible, los tests de este archivo se saltan
        // porque Login::validate() instancia User, que conecta a la BD.
        try {
            \DataBase::connection();
        } catch (\Exception $e) {
            $this->markTestSkipped('BD no disponible: ' . $e->getMessage());
        }
    }

    // TC-J01
    public function test_validate_con_email_vacio_retorna_false(): void
    {
        $login = new \Login();
        $result = $login->validate('', '1234');
        $this->assertFalse($result);
    }

    // TC-J02
    public function test_validate_con_pass_vacia_retorna_false(): void
    {
        $login = new \Login();
        $result = $login->validate('admin@test.com', '');
        $this->assertFalse($result);
    }

    // TC-J03
    public function test_validate_con_credenciales_inexistentes_retorna_false(): void
    {
        $login = new \Login();
        $result = $login->validate('noexiste_' . uniqid() . '@prueba.com', 'clave_falsa_xyz');
        $this->assertFalse($result);
    }
}
