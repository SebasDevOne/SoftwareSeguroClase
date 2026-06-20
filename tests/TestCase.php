<?php
// tests/TestCase.php
namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . '/../models/DataBase.php';
        require_once __DIR__ . '/../models/User.php';
    }
}