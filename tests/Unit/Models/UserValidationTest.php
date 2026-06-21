<?php
namespace Tests\Unit\Models;

use Tests\TestCase;

class UserValidationTest extends TestCase
{
    // --- isValidEmail ---

    // TC-J07
    public function test_email_valido_retorna_true(): void
    {
        $this->assertTrue(\User::isValidEmail('usuario@correo.com'));
    }

    // TC-J08
    public function test_email_sin_arroba_retorna_false(): void
    {
        $this->assertFalse(\User::isValidEmail('usuariocorreo.com'));
    }

    // TC-J09
    public function test_email_vacio_retorna_false(): void
    {
        $this->assertFalse(\User::isValidEmail(''));
    }

    // TC-J10
    public function test_email_sin_dominio_retorna_false(): void
    {
        $this->assertFalse(\User::isValidEmail('usuario@'));
    }

    // TC-J11
    public function test_email_solo_arroba_retorna_false(): void
    {
        $this->assertFalse(\User::isValidEmail('@'));
    }

    // --- isValidPassword ---

    // TC-J14
    public function test_password_de_6_caracteres_retorna_true(): void
    {
        $this->assertTrue(\User::isValidPassword('abc123'));
    }

    // TC-J15
    public function test_password_vacia_retorna_false(): void
    {
        $this->assertFalse(\User::isValidPassword(''));
    }

    // TC-J16
    public function test_password_menor_a_6_caracteres_retorna_false(): void
    {
        $this->assertFalse(\User::isValidPassword('abc'));
    }

    // TC-J17
    public function test_password_solo_espacios_retorna_false(): void
    {
        $this->assertFalse(\User::isValidPassword('      '));
    }
}
