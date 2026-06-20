<?php
// tests/Unit/Models/UserTest.php
namespace Tests\Unit\Models;

use Tests\TestCase;
use User;

class UserTest extends TestCase
{
    // ─── Constructores ───────────────────────────────────────────

    public function test_constructor_2_asigna_email_y_pass(): void
    {
        $user = new User('admin@test.com', '1234');

        $this->assertEquals('admin@test.com', $user->getUserEmail());
        $this->assertEquals('1234', $user->getUserPass());
    }

    public function test_constructor_8_asigna_todos_los_campos(): void
    {
        $user = new User('ROL01', 'USR001', 'Juan', 'Perez', '12345678', 'juan@test.com', 'pass123', 1);

        $this->assertEquals('ROL01',        $user->getRolCode());
        $this->assertEquals('USR001',       $user->getUserCode());
        $this->assertEquals('Juan',         $user->getUserName());
        $this->assertEquals('Perez',        $user->getUserLastName());
        $this->assertEquals('12345678',     $user->getUserId());
        $this->assertEquals('juan@test.com',$user->getUserEmail());
        $this->assertEquals('pass123',      $user->getUserPass());
        $this->assertEquals(1,              $user->getUserState());
    }

    public function test_constructor_9_asigna_todos_los_campos_con_rol_name(): void
    {
        $user = new User('ROL01', 'Administrador', 'USR001', 'Ana', 'Lopez', '87654321', 'ana@test.com', 'abc123', 1);

        $this->assertEquals('ROL01',          $user->getRolCode());
        $this->assertEquals('Administrador',  $user->getRolName());
        $this->assertEquals('Ana',            $user->getUserName());
        $this->assertEquals('ana@test.com',   $user->getUserEmail());
    }

    // ─── Getters y Setters ───────────────────────────────────────

    public function test_setRolCode_y_getRolCode(): void
    {
        $user = new User('ROL01', 'Admin', 'USR001', 'Juan', 'Perez', '123', 'j@test.com', 'pass', 1);
        $user->setRolCode('ROL99');
        $this->assertEquals('ROL99', $user->getRolCode());
    }

    public function test_setRolName_y_getRolName(): void
    {
        $user = new User('ROL01', 'Admin', 'USR001', 'Juan', 'Perez', '123', 'j@test.com', 'pass', 1);
        $user->setRolName('SuperAdmin');
        $this->assertEquals('SuperAdmin', $user->getRolName());
    }

    public function test_setUserName_y_getUserName(): void
    {
        $user = new User('ROL01', 'Admin', 'USR001', 'Juan', 'Perez', '123', 'j@test.com', 'pass', 1);
        $user->setUserName('Carlos');
        $this->assertEquals('Carlos', $user->getUserName());
    }

    public function test_setUserLastName_y_getUserLastName(): void
    {
        $user = new User('ROL01', 'Admin', 'USR001', 'Juan', 'Perez', '123', 'j@test.com', 'pass', 1);
        $user->setUserLastName('Ramirez');
        $this->assertEquals('Ramirez', $user->getUserLastName());
    }

    public function test_setUserEmail_y_getUserEmail(): void
    {
        $user = new User('ROL01', 'Admin', 'USR001', 'Juan', 'Perez', '123', 'j@test.com', 'pass', 1);
        $user->setUserEmail('nuevo@correo.com');
        $this->assertEquals('nuevo@correo.com', $user->getUserEmail());
    }

    public function test_setUserPass_y_getUserPass(): void
    {
        $user = new User('ROL01', 'Admin', 'USR001', 'Juan', 'Perez', '123', 'j@test.com', 'pass', 1);
        $user->setUserPass('nueva_pass');
        $this->assertEquals('nueva_pass', $user->getUserPass());
    }

    public function test_setUserState_activo(): void
    {
        $user = new User('ROL01', 'Admin', 'USR001', 'Juan', 'Perez', '123', 'j@test.com', 'pass', 0);
        $user->setUserState(1);
        $this->assertEquals(1, $user->getUserState());
    }

    public function test_setUserState_inactivo(): void
    {
        $user = new User('ROL01', 'Admin', 'USR001', 'Juan', 'Perez', '123', 'j@test.com', 'pass', 1);
        $user->setUserState(0);
        $this->assertEquals(0, $user->getUserState());
    }

    public function test_setUserId_y_getUserId(): void
    {
        $user = new User('ROL01', 'Admin', 'USR001', 'Juan', 'Perez', '123', 'j@test.com', 'pass', 1);
        $user->setUserId('99999999');
        $this->assertEquals('99999999', $user->getUserId());
    }

    public function test_setUserCode_y_getUserCode(): void
    {
        $user = new User('ROL01', 'Admin', 'USR001', 'Juan', 'Perez', '123', 'j@test.com', 'pass', 1);
        $user->setUserCode('USR999');
        $this->assertEquals('USR999', $user->getUserCode());
    }

    // ─── Tests adicionales (plan de pruebas) ─────────────────────

    // TC-J04
    public function test_constructor_0_crea_instancia_sin_parametros(): void
    {
        try {
            \DataBase::connection();
        } catch (\Exception $e) {
            $this->markTestSkipped('BD no disponible: ' . $e->getMessage());
        }
        $user = new User();
        $this->assertInstanceOf(User::class, $user);
    }

    // TC-J05
    public function test_login_con_credenciales_invalidas_retorna_false(): void
    {
        try {
            \DataBase::connection();
        } catch (\Exception $e) {
            $this->markTestSkipped('BD no disponible: ' . $e->getMessage());
        }
        $user = new User('correo@invalido.com', 'claveincorrecta');
        $result = $user->login();
        $this->assertFalse($result);
    }

    // TC-J06
    public function test_login_con_usuario_inexistente_retorna_false(): void
    {
        try {
            \DataBase::connection();
        } catch (\Exception $e) {
            $this->markTestSkipped('BD no disponible: ' . $e->getMessage());
        }
        // uniqid() garantiza que este email no existe en la tabla USERS
        $user = new User('noexiste_' . uniqid() . '@prueba.com', 'cualquier_clave');
        $result = $user->login();
        $this->assertFalse($result);
    }
}