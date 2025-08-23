<p align="center"> <a href="https://laravel.com" target="_blank"> <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Logo Laravel"> </a> </p> <p align="center"> <a href="https://github.com/tuusuario/tuproyecto/actions"><img src="https://github.com/tuusuario/tuproyecto/workflows/tests/badge.svg" alt="Estado del build"></a> <a href="https://packagist.org/packages/laravel/laravel"><img src="https://img.shields.io/packagist/dt/laravel/laravel" alt="Descargas totales"></a> <a href="https://packagist.org/packages/laravel/laravel"><img src="https://img.shields.io/packagist/v/laravel/laravel" alt="Última versión estable"></a> <a href="https://opensource.org/licenses/MIT"><img src="https://img.shields.io/packagist/l/laravel/laravel" alt="Licencia"></a> </p>
Acerca del Proyecto

Este es un API REST desarrollado con Laravel 10, que incluye:

Autenticación mediante Laravel Sanctum (tokens API)

Integración de inicio de sesión con Google OAuth2 (Laravel Socialite)

Gestión de usuarios (registro, inicio de sesión, cierre de sesión)

Endpoints para administración de vehículos

Hashing seguro de contraseñas (bcrypt/argon2id)

Está diseñado como base sólida para proyectos que necesiten login local y autenticación social.

Características

Autenticación con Laravel Sanctum

Login con Google OAuth2

Endpoints REST para Vehículos

Hashing seguro de contraseñas

Migrations y ORM Eloquent

Endpoints del API
Públicos

GET /api/ping – Verifica el estado del API

POST /api/auth/register – Registro de usuario

POST /api/auth/login – Inicio de sesión local (email/contraseña)

GET /api/auth/google/redirect – Redirige a Google para autenticación

GET /api/auth/google/callback – Procesa el login de Google

Protegidos (requieren Bearer <token> generado por Sanctum)

GET /api/auth/me – Devuelve datos del usuario autenticado

POST /api/auth/logout – Revoca el token actual

GET /api/vehicles – Lista todos los vehículos

POST /api/vehicles – Crea un nuevo vehículo

GET /api/vehicles/{vehicle} – Muestra un vehículo específico

Instalación

Clonar el repositorio:

git clone https://github.com/tuusuario/tuproyecto.git
cd tuproyecto


Instalar dependencias:

composer install


Crear archivo .env y configurarlo:

cp .env.example .env
php artisan key:generate


Ejecutar migraciones:

php artisan migrate


Iniciar servidor:

php artisan serve

Pruebas con cURL

Registro:

curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Juan","email":"juan@example.com","password":"1234"}'


Inicio de sesión:

curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"juan@example.com","password":"1234"}'


Obtener vehículos (requiere token):

curl -X GET http://localhost:8000/api/vehicles \
  -H "Authorization: Bearer <tu_token_aquí>"

Aprender Laravel

Documentación oficial de Laravel

Laravel Bootcamp

Laracasts
 – Miles de tutoriales en video sobre Laravel, PHP y más.

Contribuir

¡Las contribuciones son bienvenidas!
Por favor, revisa la guía de contribución
 antes de enviar un pull request.

Vulnerabilidades de Seguridad

Si descubres una vulnerabilidad de seguridad, por favor repórtala mediante GitHub Issues
 o contacta directamente a los mantenedores del proyecto.

Licencia

Este proyecto es software de código abierto licenciado bajo la Licencia MIT
.