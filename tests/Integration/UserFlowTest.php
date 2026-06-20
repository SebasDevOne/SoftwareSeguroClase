<?php
namespace Tests\Integration;

use Tests\TestCase;
use User;
use DataBase;

class UserFlowTest extends TestCase
{
    private \PDO $pdo;
    private array $userCodesToClean = [];
    private array $rolCodesToClean  = [];

    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->pdo = DataBase::connection();
        } catch (\Exception $e) {
            $this->markTestSkipped('BD no disponible: ' . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->userCodesToClean as $code) {
            $this->pdo->prepare('DELETE FROM USERS WHERE user_code = ?')->execute([$code]);
        }
        foreach ($this->rolCodesToClean as $code) {
            $this->pdo->prepare('DELETE FROM ROLES WHERE rol_code = ?')->execute([$code]);
        }
        $this->userCodesToClean = [];
        $this->rolCodesToClean  = [];
        parent::tearDown();
    }

    // TC-JD01
    public function test_login_exitoso_retorna_objeto_user(): void
    {
        $user   = new User('profealbeiro2020@gmail.com', '12345');
        $result = $user->login();

        $this->assertInstanceOf(User::class, $result);
        $this->assertNotEmpty($result->getRolName());
    }

    // TC-JD02
    public function test_login_usuario_inactivo_retorna_user_con_state_0(): void
    {
        // Crear usuario inactivo propio para no depender de datos externos
        $userCode = 'TMP_' . uniqid();
        $email    = 'inactive_' . time() . '@test.com';
        $this->userCodesToClean[] = $userCode;

        $inactivo = new User('1', $userCode, 'Inactivo', 'Test', '00000001', $email, '12345', 0);
        $inactivo->create_user();

        $user   = new User($email, '12345');
        $result = $user->login();

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(0, $result->getUserState());
    }

    // TC-JD03
    public function test_crear_y_leer_usuario(): void
    {
        $userCode    = 'TMP_' . uniqid();
        $emailPrueba = 'test_jd03_' . time() . '@test.com';
        $this->userCodesToClean[] = $userCode;

        $nuevoUsuario = new User(
            '1', $userCode, 'David', 'Vargas',
            '45698725', $emailPrueba, '12345', 1
        );
        $nuevoUsuario->create_user();

        $lector   = new User();
        $usuarios = $lector->read_users();
        $emails   = array_map(fn($u) => $u->getUserEmail(), $usuarios);

        $this->assertContains($emailPrueba, $emails);
    }

    // TC-JD04
    public function test_eliminar_usuario_no_aparece_en_lista(): void
    {
        $userCode    = 'TMP_' . uniqid();
        $emailPrueba = 'test_jd04_' . time() . '@test.com';

        $nuevoUsuario = new User(
            '1', $userCode, 'Eliminar', 'Prueba',
            '99999999', $emailPrueba, '12345', 1
        );
        $nuevoUsuario->create_user();

        $lector = new User();
        $lector->delete_user($userCode);

        $usuarios   = $lector->read_users();
        $codigos    = array_map(fn($u) => $u->getUserCode(), $usuarios);

        $this->assertNotContains($userCode, $codigos);
    }

    // TC-JD05
    public function test_crear_y_leer_rol(): void
    {
        $rolCode = (string) rand(900, 999);
        $this->rolCodesToClean[] = $rolCode;

        $nuevoRol = new User();
        $nuevoRol->setRolCode($rolCode);
        $nuevoRol->setRolName('Rol de Prueba');
        $nuevoRol->create_rol();

        $lector = new User();
        $roles  = $lector->read_roles();
        $codigos = array_map(fn($r) => $r->getRolCode(), $roles);

        $this->assertContains($rolCode, $codigos);
    }

    // TC-JD06
    public function test_eliminar_rol_no_aparece_en_lista(): void
    {
        $rolCode = (string) rand(900, 999);

        $nuevoRol = new User();
        $nuevoRol->setRolCode($rolCode);
        $nuevoRol->setRolName('Rol Temporal');
        $nuevoRol->create_rol();

        $lector = new User();
        $lector->delete_rol($rolCode);

        $roles   = $lector->read_roles();
        $codigos = array_map(fn($r) => $r->getRolCode(), $roles);

        $this->assertNotContains($rolCode, $codigos);
    }
}
