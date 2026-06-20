<?php
// tests/Unit/Controllers/UsersTest.php
namespace Tests\Unit\Controllers;

use Tests\TestCase;

class UsersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . '/../../../controllers/Users.php';
        try {
            \DataBase::connection();
        } catch (\Exception $e) {
            $this->markTestSkipped('BD no disponible: ' . $e->getMessage());
        }
    }

    // TC-JD07
    public function test_users_controller_instancia_correctamente(): void
    {
        $users = new \Users();
        $this->assertInstanceOf(\Users::class, $users);
    }
}
