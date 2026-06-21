<?php
namespace Tests\Integration;

use Tests\TestCase;
use User;
use DataBase;

class UserValidationIntegrationTest extends TestCase
{
    private \PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();
        try {
            $this->pdo = DataBase::connection();
        } catch (\Exception $e) {
            $this->markTestSkipped('BD no disponible: ' . $e->getMessage());
        }
    }

    private function userExistsInDb(string $userCode): bool
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM USERS WHERE user_code = ?');
        $stmt->execute([$userCode]);
        return (int)$stmt->fetchColumn() > 0;
    }

    // TC-J12
    public function test_create_user_con_email_invalido_retorna_false(): void
    {
        $user = new User('1', 'TMP_V1_' . uniqid(), 'Test', 'Email', '00000001', 'email_sin_arroba', '12345', 1);
        $this->assertFalse($user->create_user());
    }

    // TC-J13
    public function test_create_user_con_email_invalido_no_inserta_en_bd(): void
    {
        $userCode = 'TMP_V2_' . uniqid();
        $user = new User('1', $userCode, 'Test', 'Email', '00000001', 'emailsinarroba', '12345', 1);
        $user->create_user();
        $this->assertFalse($this->userExistsInDb($userCode));
    }

    // TC-J18
    public function test_create_user_con_password_vacia_retorna_false(): void
    {
        $user = new User('1', 'TMP_V3_' . uniqid(), 'Test', 'Pass', '00000001', 'valido@test.com', '', 1);
        $this->assertFalse($user->create_user());
    }

    // TC-J19
    public function test_create_user_con_password_corta_no_inserta_en_bd(): void
    {
        $userCode = 'TMP_V4_' . uniqid();
        $user = new User('1', $userCode, 'Test', 'Pass', '00000001', 'valido@test.com', 'abc', 1);
        $user->create_user();
        $this->assertFalse($this->userExistsInDb($userCode));
    }

    // TC-J20
    public function test_create_user_con_nombre_vacio_retorna_false(): void
    {
        $user = new User('1', 'TMP_V5_' . uniqid(), '', 'Apellido', '00000001', 'valido@test.com', '123456', 1);
        $this->assertFalse($user->create_user());
    }

    // TC-J21
    public function test_create_user_con_apellido_vacio_retorna_false(): void
    {
        $user = new User('1', 'TMP_V6_' . uniqid(), 'Nombre', '', '00000001', 'valido@test.com', '123456', 1);
        $this->assertFalse($user->create_user());
    }

    // TC-J22
    public function test_create_user_con_datos_validos_inserta_en_bd(): void
    {
        // Crear rol temporal propio para garantizar FK válida y INNER JOIN correcto
        $rolCode = rand(800, 899);
        $rol = new User();
        $rol->setRolCode($rolCode);
        $rol->setRolName('Rol Temp Validacion');
        $rol->create_rol();

        $userCode = rand(70000, 79999);
        $userId   = rand(10000000, 99999999);
        $email    = 'valido_' . uniqid() . '@test.com';
        $user = new User($rolCode, $userCode, 'Nombre', 'Apellido', $userId, $email, '123456', 1);
        $user->create_user();

        $lector  = new User();
        $codigos = array_map(fn($u) => $u->getUserCode(), $lector->read_users());
        $existe  = in_array($userCode, $codigos, true);

        // Limpiar
        $this->pdo->prepare('DELETE FROM USERS WHERE user_code = ?')->execute([$userCode]);
        $this->pdo->prepare('DELETE FROM ROLES WHERE rol_code = ?')->execute([$rolCode]);

        $this->assertTrue($existe, "El usuario '$userCode' deberia existir en la BD tras create_user()");
    }
}
