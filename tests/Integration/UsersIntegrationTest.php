<?php
namespace Tests\Integration;

use Tests\TestCase;
use DataBase;

class UsersIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $_SESSION['session'] = 'test';
        require_once __DIR__ . '/../../controllers/Users.php';
        try {
            DataBase::connection();
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
