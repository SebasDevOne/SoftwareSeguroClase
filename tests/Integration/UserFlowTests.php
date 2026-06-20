<?php
namespace Tests\Integration;

use Tests\TestCase;
use User;

class UserFlowTests extends TestCase
{
    // TC-JD01
    public function test_login_exitoso_retorna_objeto_user(): void
    {
        $user = new User('profealbeiro2020@gmail.com', '12345');
        $result = $user->login();

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('admin', $result->getRolName());
        $this->assertNotEmpty($result->getRolName());
    }


        // TC-JD02
    public function test_login_usuario_inactivo_retorna_user_con_state_0(): void
    {
        $user = new User('jair@test.com', '12345');
        $result = $user->login();

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(0, $result->getUserState());
    }

    // TC-JD03
    public function test_crear_y_leer_usuario(): void
    {
        // Crear usuario de prueba con email único para evitar choques
        $emailPrueba = 'test_jd03_' . time() . '@test.com';

        $nuevoUsuario = new User(
            '1',           // rol_code existente en tu BD
            '3',        // user_code único
            'David',
            'Vargas',
            '45698725',
            $emailPrueba,
            '12345',
            1
        );
        $nuevoUsuario->create_user();

        // Leer todos los usuarios
        $lector = new User();
        $usuarios = $lector->read_users();

        // Buscar el email insertado dentro del array
        $emails = array_map(fn($u) => $u->getUserEmail(), $usuarios);

        $this->assertContains($emailPrueba, $emails);
    }
}