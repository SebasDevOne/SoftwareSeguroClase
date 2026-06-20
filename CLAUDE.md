# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Correr todos los tests
vendor/bin/phpunit

# Correr solo suite Unit
vendor/bin/phpunit --testsuite Unit

# Correr solo suite Integration
vendor/bin/phpunit --testsuite Integration

# Correr un archivo de test específico
vendor/bin/phpunit tests/Unit/Models/UserTest.php

# Instalar dependencias
composer install
```

## Arquitectura

Aplicación PHP sin framework, con patrón MVC casero enrutado por `index.php`.

**Enrutamiento** (`index.php`): lee `$_REQUEST['c']` para elegir el controlador y `$_REQUEST['a']` para la acción. Si la sesión está vacía, redirige a `?` (Landing). Las vistas de header/footer varían según el rol del usuario en sesión (`$_SESSION['session']` contiene el nombre del rol, p. ej. `Administrador`), y se cargan desde `views/roles/<rol_name>/`.

**Base de datos** (`models/DataBase.php`): clase estática con un único método `DataBase::connection()` que retorna un PDO. Conexión hardcodeada a `localhost:3307`, base `db_inventory`, usuario `root` sin contraseña.

**Modelo `User`** (`models/User.php`): clase que agrupa tanto la entidad `User` como la entidad `Rol`. Usa sobrecarga de constructores simulada vía `__constructN()` según la cantidad de parámetros (0, 2, 8 o 9). Contiene toda la lógica de persistencia: CRUD de usuarios y roles, y autenticación. Las contraseñas se hashean con `sha1()` antes de persistir o comparar.

**Sesión**: en `Login.php`, tras autenticar, el objeto `User` completo se serializa en `$_SESSION['profile']`. En `index.php` se deserializa con `unserialize()` para tener el perfil disponible en todas las vistas.

**Tests**: PHPUnit 11. La clase base `Tests\TestCase` requiere `DataBase.php` y `User.php` en `setUp`. Los tests unitarios de modelo prueban constructores y getters/setters sin tocar la BD (el constructor de 9 parámetros hace `unset($this->dbh)`, por eso no necesita conexión). Los tests de integración sí requieren BD activa.

## Nota de seguridad conocida

`sha1()` para contraseñas y `unserialize()` sobre datos de sesión son deudas técnicas de seguridad documentadas en el proyecto (el contexto es un curso de desarrollo seguro). Al proponer mejoras, señalar explícitamente estas dos áreas.
