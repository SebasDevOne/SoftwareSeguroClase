<?php
// tests/Integration/AuthIntegrationTest.php
namespace Tests\Integration;

use Tests\TestCase;
use User;
use DataBase;

class AuthIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        try {
            DataBase::connection();
        } catch (\Exception $e) {
            $this->markTestSkipped('BD no disponible: ' . $e->getMessage());
        }
    }

    // TC-J04
    public function test_constructor_0_crea_instancia_sin_parametros(): void
    {
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
    }

    // TC-J05
    public function test_login_con_credenciales_invalidas_retorna_false(): void
    {
        $user = new User('correo@invalido.com', 'claveincorrecta');
        $result = $user->login();
        $this->assertFalse($result);
    }

    // TC-J06
    public function test_login_con_usuario_inexistente_retorna_false(): void
    {
        $user = new User('noexiste_' . uniqid() . '@prueba.com', 'cualquier_clave');
        $result = $user->login();
        $this->assertFalse($result);
    }

}
