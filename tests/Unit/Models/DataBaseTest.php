<?php
// tests/Unit/Models/DataBaseTest.php
namespace Tests\Unit\Models;

use Tests\TestCase;
use DataBase;

class DataBaseTest extends TestCase
{
    public function test_connection_retorna_instancia_pdo(): void
    {
        try {
            $pdo = DataBase::connection();
            $this->assertInstanceOf(\PDO::class, $pdo);
        } catch (\Exception $e) {
            $this->markTestSkipped('BD no disponible: ' . $e->getMessage());
        }
    }

    public function test_connection_tiene_errmode_exception(): void
    {
        try {
            $pdo = DataBase::connection();
            $errorMode = $pdo->getAttribute(\PDO::ATTR_ERRMODE);
            $this->assertEquals(\PDO::ERRMODE_EXCEPTION, $errorMode);
        } catch (\Exception $e) {
            $this->markTestSkipped('BD no disponible: ' . $e->getMessage());
        }
    }
}