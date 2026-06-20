# Plan de Pruebas — Desarrollo de Software Seguro

**Autores:** Juan Sebastián (apellido) y Jair David (apellido)  
**Institución:** [Nombre de la institución]  
**Curso:** Desarrollo de Software Seguro  
**Docente:** [Nombre del docente]  
**Fecha:** 20 de junio de 2026

---

## 1. Introducción

El presente documento describe el plan de pruebas para el proyecto de inventario desarrollado en el curso de Desarrollo de Software Seguro. La aplicación está construida en PHP sin framework, siguiendo un patrón MVC artesanal, con una base de datos MySQL y PHPUnit 11 como herramienta de pruebas automatizadas.

El plan abarca pruebas unitarias y de integración, y está distribuido equitativamente entre los dos integrantes del equipo.

### 1.1 Aclaración sobre las pruebas existentes

Las pruebas que actualmente se encuentran en el repositorio (`tests/Unit/Models/UserTest.php`, `tests/Unit/Models/DataBaseTest.php`, `tests/Unit/Controllers/LoginTests.php`, `tests/Unit/Controllers/UsersTest.php`) fueron realizadas durante las sesiones de clase como ejercicio guiado con el docente. Dichas pruebas **no forman parte del presente plan de pruebas** y se documentan únicamente como antecedente.

Como parte de la elaboración de este plan, se realizó un análisis crítico de esas pruebas existentes. A continuación se presentan los hallazgos.

---

## 2. Análisis de las Pruebas Existentes

Durante la revisión del código de pruebas ya entregado se identificaron los siguientes problemas:

### 2.1 `tests/Unit/Models/UserTest.php`

**Estado:** Aceptable, con observaciones menores.

Cubre los tres constructores sobrecargados (`__construct2`, `__construct8`, `__construct9`) y todos los getters/setters. Los 11 casos de prueba son correctos y ejecutan sin requerir conexión a base de datos, dado que el constructor de 9 parámetros hace `unset($this->dbh)` explícitamente.

**Observación:** No se prueba el constructor de 0 parámetros (`__construct0`) ni los métodos de persistencia (`login()`, `create_user()`, `read_users()`, etc.). Esto queda fuera del alcance de la prueba unitaria pura, pero debería cubrirse en integración.

### 2.2 `tests/Unit/Models/DataBaseTest.php`

**Estado:** Correcto.

Verifica que `DataBase::connection()` retorna una instancia PDO y que el modo de error es `ERRMODE_EXCEPTION`. Ambos tests están correctamente envueltos en `try/catch` con `markTestSkipped()` cuando la base de datos no está disponible, lo cual es una buena práctica para entornos sin conexión.

### 2.3 `tests/Unit/Controllers/LoginTests.php`

**Estado:** Inválido — requiere refactorización.**

Este es el hallazgo más crítico del análisis. Los dos tests llaman al método `validate()` sobre una instancia de `Login`:

```php
$login = new \Login();
$result = $login->validate('', '');
```

Sin embargo, la clase `Login` **no tiene un método `validate()`**. Su único método público es `main()`, que lee directamente de `$_POST`, `$_SERVER` y `$_SESSION`, e incluye archivos de vista (`require_once`). Ejecutar estos tests produce un error fatal de PHP (`Call to undefined method Login::validate()`).

**Causa raíz:** El controlador fue diseñado sin separación de lógica de negocio, lo que lo hace no testeable unitariamente en su estado actual. La solución es extraer la lógica de validación a un método `validate()` real dentro del controlador, o mover esa lógica al modelo.

**Decisión:** Estas pruebas se reemplazan en el presente plan por casos correctamente diseñados que prueban la lógica de autenticación a través del modelo `User`, donde esa lógica realmente reside.

### 2.4 `tests/Unit/Controllers/UsersTest.php`

**Estado:** Placeholder vacío.**

Contiene un único test `test_placeholder()` que siempre pasa (`assertTrue(true)`). No prueba ninguna funcionalidad. Se reemplaza en el presente plan.

### 2.5 `tests/Integration/UserFlowTests.php`

**Estado:** Archivo vacío.**

El archivo existe pero no contiene ningún test. Corresponde implementarlo como parte del presente plan.

---

## 3. Alcance del Plan de Pruebas

### Incluido

- Lógica de negocio del modelo `User`: autenticación, constructores, getters/setters, CRUD de usuarios y roles (con base de datos activa).
- Lógica de autenticación del controlador `Login` tras refactorización mínima.
- Flujos de integración: inicio de sesión, gestión de usuarios y roles.

### Excluido

- Pruebas de interfaz gráfica (vistas `.view.php`).
- Pruebas de rendimiento o carga.
- Pruebas de seguridad automáticas (análisis de SHA1, deserialización insegura — se documentan como hallazgos pero no como casos de prueba ejecutables en este plan).

---

## 4. Ambiente de Pruebas

| Componente | Detalle |
|---|---|
| Lenguaje | PHP 8.x |
| Framework de pruebas | PHPUnit 11 |
| Base de datos | MySQL en `localhost:3307`, base `db_inventory` |
| Comando para correr todos los tests | `vendor/bin/phpunit` |
| Comando para una suite | `vendor/bin/phpunit --testsuite Unit` |
| Comando para un archivo | `vendor/bin/phpunit tests/Unit/Models/UserTest.php` |

---

## 5. Pruebas Asignadas — Juan Sebastián

Esta sección corresponde a la porción del plan a cargo de Juan Sebastián. Se cubren pruebas unitarias del modelo `User` orientadas a la persistencia, y la refactorización + prueba del controlador `Login`.

### 5.1 Refactorización previa del controlador Login

Para que `LoginTests.php` sea ejecutable, se debe extraer la lógica de validación de credenciales al modelo `User` (o a un método `validate()` en el controlador que reciba parámetros, sin depender de superglobales). Se propone agregar a la clase `Login` el siguiente método:

```php
public function validate(string $email, string $pass): bool {
    $user = new \User($email, $pass);
    return $user->login() !== false;
}
```

Este método es testeable unitariamente con una base de datos de prueba, o mediante un mock de `User`.

### 5.2 Casos de Prueba — Juan Sebastián

#### Archivo: `tests/Unit/Controllers/LoginTests.php` (reescrito)

| ID | Nombre del test | Descripción | Resultado esperado |
|---|---|---|---|
| TC-J01 | `test_validate_con_email_vacio_retorna_false` | Llama `validate('', '1234')` | `false` |
| TC-J02 | `test_validate_con_pass_vacia_retorna_false` | Llama `validate('admin@test.com', '')` | `false` |
| TC-J03 | `test_validate_con_credenciales_inexistentes_retorna_false` | Usuario que no existe en BD | `false` |

#### Archivo: `tests/Unit/Models/UserTest.php` (casos adicionales)

| ID | Nombre del test | Descripción | Resultado esperado |
|---|---|---|---|
| TC-J04 | `test_constructor_0_crea_instancia_sin_parametros` | `new User()` no lanza excepción (si no hay BD activa, skip) | Instancia válida o skip |
| TC-J05 | `test_login_con_credenciales_invalidas_retorna_false` | `User->login()` con email/pass incorrectos | `false` |
| TC-J06 | `test_login_con_usuario_inexistente_retorna_false` | Email que no existe en la tabla USERS | `false` |

---

## 6. Pruebas Asignadas — Jair David

Esta sección corresponde a la porción del plan a cargo de Jair David. Se cubren pruebas de integración que verifican el flujo completo del sistema con base de datos activa, y los tests del controlador `Users`.

### 6.1 Casos de Prueba — Jair David

#### Archivo: `tests/Integration/UserFlowTests.php` (implementar)

| ID | Nombre del test | Descripción | Resultado esperado |
|---|---|---|---|
| TC-JD01 | `test_login_exitoso_retorna_objeto_user` | Credenciales válidas de un usuario activo en BD | Instancia de `User` con `getRolName()` no vacío |
| TC-JD02 | `test_login_usuario_inactivo_retorna_user_con_state_0` | Credenciales válidas pero `user_state = 0` | `User->getUserState() === 0` |
| TC-JD03 | `test_crear_y_leer_usuario` | `create_user()` seguido de `read_users()` incluye el usuario creado | El array resultante contiene el `user_email` insertado |
| TC-JD04 | `test_eliminar_usuario_no_aparece_en_lista` | `create_user()` → `delete_user()` → `read_users()` | El `user_code` eliminado no está en la lista |
| TC-JD05 | `test_crear_y_leer_rol` | `create_rol()` → `read_roles()` | El array contiene el `rol_code` creado |
| TC-JD06 | `test_eliminar_rol_no_aparece_en_lista` | `create_rol()` → `delete_rol()` → `read_roles()` | El `rol_code` eliminado no está en la lista |

#### Archivo: `tests/Unit/Controllers/UsersTest.php` (reemplazar placeholder)

| ID | Nombre del test | Descripción | Resultado esperado |
|---|---|---|---|
| TC-JD07 | `test_users_controller_instancia_correctamente` | `new \Users()` no lanza excepción | Instancia válida |

### 6.2 Instrucciones para Jair David

Jair David, a continuación se detallan tus responsabilidades dentro del plan de pruebas:

**Lo que debes hacer:**

1. **Implementar `tests/Integration/UserFlowTests.php`** con los 6 casos de prueba descritos en la tabla TC-JD01 a TC-JD06. Cada test debe:
   - Conectarse a la base de datos real usando `DataBase::connection()`.
   - Crear los datos de prueba al inicio del test (`setUp` o directamente en el test).
   - Limpiar los datos al finalizar (`tearDown`) para no contaminar otros tests.
   - Usar `markTestSkipped()` si la base de datos no está disponible (igual que hace `DataBaseTest`).

2. **Reemplazar el placeholder en `tests/Unit/Controllers/UsersTest.php`** con el caso TC-JD07.

3. **Estructura base sugerida para los tests de integración:**

```php
<?php
namespace Tests\Integration;

use Tests\TestCase;
use User;
use DataBase;

class UserFlowTests extends TestCase
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

    protected function tearDown(): void
    {
        // Limpiar datos de prueba aquí
        parent::tearDown();
    }

    public function test_login_exitoso_retorna_objeto_user(): void
    {
        // Implementar TC-JD01
    }

    // ... demás tests
}
```

4. **Convención de nombres:** mantener el patrón `test_<acción>_<condición>_<resultado_esperado>()` que ya usa el proyecto.

5. **Correr tus tests** con: `vendor/bin/phpunit --testsuite Integration`

---

## 7. Hallazgos de Seguridad (No Ejecutables como Tests)

Durante el análisis del código se identificaron dos deudas técnicas de seguridad relevantes para el contexto del curso:

1. **Uso de SHA1 para contraseñas** (`models/User.php`, líneas 150 y 270): SHA1 es un algoritmo roto para almacenamiento de contraseñas. Se recomienda reemplazar por `password_hash()` / `password_verify()` con `PASSWORD_BCRYPT` o `PASSWORD_ARGON2ID`.

2. **`unserialize()` sobre datos de sesión** (`index.php`, línea 19): deserializar objetos de sesión con `unserialize()` puede permitir ataques de Object Injection si el contenido de la sesión es manipulado. Se recomienda reconstruir el objeto desde la base de datos, o usar `json_decode()` con una representación plana.

Estos hallazgos se documentan como parte del análisis pero quedan fuera del alcance de los tests automatizados de este plan.

---

## 8. Criterios de Aceptación

- Todos los tests del plan deben ejecutar sin errores fatales de PHP.
- Los tests que dependen de BD deben usar `markTestSkipped()` cuando la conexión no está disponible.
- Ningún test debe dejar datos residuales en la base de datos (cleanup en `tearDown`).
- La suite completa se corre con `vendor/bin/phpunit` sin parámetros adicionales.

---

## Referencias

PHPUnit Project. (2024). *PHPUnit Manual* (versión 11). https://phpunit.de/documentation.html

American Psychological Association. (2020). *Publication manual of the American Psychological Association* (7.ª ed.). https://doi.org/10.1037/0000165-000
