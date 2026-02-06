VIDEO DE LA TAREA: https://drive.google.com/file/d/15X2lHlYgNTr7zSW4szPV9IOq_xvwmUFj/view?usp=drive_link

# Arquitectura de Microservicios con Lumen

Este proyecto implementa una arquitectura de microservicios utilizando Laravel Lumen para gestionar autores y libros, con un API Gateway que actúa como punto de entrada único para todos los servicios.

## 📋 Descripción

El proyecto está compuesto por tres microservicios independientes:

1. **LumenAuthorsApi**: Microservicio dedicado a la gestión de autores
2. **LumenBooksApi**: Microservicio dedicado a la gestión de libros
3. **LumenGatewayApi**: API Gateway que orquesta las peticiones a los microservicios

## 🏗️ Arquitectura

```
┌─────────────────┐
│   Cliente API   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  API Gateway    │  ◄─── Punto de entrada único
│ LumenGatewayApi │
└────────┬────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌─────────┐ ┌─────────┐
│ Authors │ │  Books  │
│ Service │ │ Service │
└─────────┘ └─────────┘
    │           │
    ▼           ▼
┌─────────┐ ┌─────────┐
│ Authors │ │  Books  │
│   DB    │ │   DB    │
└─────────┘ └─────────┘
```

> 📖 **Documentación detallada**: Para una explicación completa de la arquitectura con diagramas interactivos, consulta [arquitectura.md](arquitectura.md)

### Características de la Arquitectura

- **Separación de responsabilidades**: Cada microservicio gestiona su propio dominio
- **Bases de datos independientes**: Cada servicio tiene su propia base de datos SQLite
- **Comunicación HTTP**: El Gateway se comunica con los microservicios mediante peticiones HTTP usando Guzzle
- **Respuestas estandarizadas**: Todos los servicios utilizan el trait `ApiResponser` para respuestas consistentes
- **Patrón API Gateway**: Punto de entrada único que orquesta y valida las peticiones
- **Database per Service**: Cada microservicio mantiene su propia base de datos independiente

## 📁 Estructura del Proyecto

```
arquitecturaMicroServicios/
├── LumenAuthorsApi/          # Microservicio de Autores
│   ├── app/
│   │   ├── Author.php        # Modelo Eloquent
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       └── AuthorController.php
│   │   └── Traits/
│   │       └── ApiResponser.php
│   ├── database/
│   │   └── migrations/
│   │       └── create_authors_table.php
│   ├── tests/                # Tests unitarios
│   └── routes/
│       └── web.php
│
├── LumenBooksApi/            # Microservicio de Libros
│   ├── app/
│   │   ├── Book.php          # Modelo Eloquent
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       └── BookController.php
│   │   └── Traits/
│   │       └── ApiResponser.php
│   ├── database/
│   │   └── migrations/
│   │       └── create_books_table.php
│   ├── tests/                # Tests unitarios
│   └── routes/
│       └── web.php
│
├── LumenGatewayApi/          # API Gateway
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       ├── AuthorController.php
│   │   │       └── BookController.php
│   │   ├── Services/
│   │   │   ├── AuthorService.php
│   │   │   └── BookService.php
│   │   └── Traits/
│   │       ├── ApiResponser.php
│   │       └── ConsumesExternalService.php
│   ├── config/
│   │   └── services.php      # Configuración de URLs de servicios
│   ├── tests/                # Tests unitarios
│   └── routes/
│       └── web.php
│
├── docs/                     # Documentación de APIs
│   ├── api-authors-openapi.yaml
│   ├── api-books-openapi.yaml
│   ├── api-gateway-openapi.yaml
│   ├── api-authors.html      # Swagger UI para Authors API
│   ├── api-books.html        # Swagger UI para Books API
│   ├── api-gateway.html      # Swagger UI para Gateway API
│   ├── index.html            # Página principal de documentación
│   └── servir-docs.sh        # Script para servir documentación
│
├── pipeline/                 # Configuración CI/CD
│   ├── ci.yml                # GitHub Actions workflow
│   ├── .gitlab-ci.yml        # GitLab CI configuration
│   ├── local-test.sh         # Script de validación local
│   ├── docker-compose.test.yml
│   └── README.md             # Documentación del pipeline
│
├── .github/
│   └── workflows/
│       └── ci.yml            # GitHub Actions workflow
│
├── arquitectura.md           # Documentación de arquitectura
├── guiaEstudiante.md         # Guía para estudiantes
├── test_api.sh              # Script de pruebas de APIs
├── test_gateway_simple.sh   # Script de pruebas del Gateway
└── README.md                 # Este archivo
```

## 🔧 Requisitos

- **PHP**: >= 8.2 (recomendado 8.2+)
- **Composer**: Para gestionar dependencias
- **SQLite**: Para las bases de datos (incluido en PHP)

> **Nota**: Este proyecto ha sido actualizado a **Lumen 10.x** (requiere PHP 8.2+ debido a las dependencias actualizadas). Las versiones más recientes de las dependencias requieren PHP 8.2 o superior.

## 📦 Instalación

### 1. Clonar o descargar el proyecto

```bash
cd arquitecturaMicroServicios
```

### 2. Instalar dependencias para cada microservicio

```bash
# Authors Service
cd LumenAuthorsApi
composer install

# Books Service
cd ../LumenBooksApi
composer install

# Gateway Service
cd ../LumenGatewayApi
composer install
```

> **Importante**: Si estás actualizando desde una versión anterior, ejecuta `composer update` en lugar de `composer install` para obtener las últimas versiones compatibles. Ver [ACTUALIZACION_LUMEN10.md](ACTUALIZACION_LUMEN10.md) para más detalles.

### 3. Configurar variables de entorno

Cada servicio necesita un archivo `.env`. Puedes crear uno basándote en `.env.example` si existe, o crear uno nuevo.

#### LumenAuthorsApi/.env
```env
APP_NAME=LumenAuthorsApi
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8001
APP_TIMEZONE=UTC

DB_CONNECTION=sqlite
DB_DATABASE=/ruta/completa/a/database/database.sqlite

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=
```

#### LumenBooksApi/.env
```env
APP_NAME=LumenBooksApi
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8002
APP_TIMEZONE=UTC

DB_CONNECTION=sqlite
DB_DATABASE=/ruta/completa/a/database/database.sqlite

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=
```

#### LumenGatewayApi/.env
```env
APP_NAME=LumenGatewayApi
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=UTC

AUTHORS_SERVICE_BASE_URL=http://localhost:8001
AUTHORS_SERVICE_SECRET=

BOOKS_SERVICE_BASE_URL=http://localhost:8002
BOOKS_SERVICE_SECRET=

LOG_CHANNEL=stack
LOG_SLACK_WEBHOOK_URL=
```

### 4. Ejecutar migraciones

```bash
# En cada servicio
php artisan migrate
```

### 5. Iniciar los servidores

Abre tres terminales diferentes:

**Terminal 1 - Authors Service:**
```bash
cd LumenAuthorsApi
php -S localhost:8001 -t public
```

**Terminal 2 - Books Service:**
```bash
cd LumenBooksApi
php -S localhost:8002 -t public
```

**Terminal 3 - Gateway:**
```bash
cd LumenGatewayApi
php -S localhost:8000 -t public
```

## 🚀 Uso de la API

### Base URL

- **Gateway**: `http://localhost:8000`
- **Authors Service**: `http://localhost:8001`
- **Books Service**: `http://localhost:8002`

> **Nota**: Se recomienda usar siempre el Gateway como punto de entrada, ya que este valida las relaciones entre entidades (por ejemplo, verifica que un autor exista antes de crear un libro).

## 📚 Endpoints

### Autores (Authors)

#### Obtener todos los autores
```http
GET /authors
```

**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Gabriel García Márquez",
      "gender": "male",
      "country": "Colombia",
      "created_at": "2020-11-20 15:19:58",
      "updated_at": "2020-11-20 15:19:58"
    }
  ]
}
```

#### Obtener un autor específico
```http
GET /authors/{id}
```

#### Crear un autor
```http
POST /authors
Content-Type: application/json

{
  "name": "Isabel Allende",
  "gender": "female",
  "country": "Chile"
}
```

**Validaciones:**
- `name`: requerido, máximo 255 caracteres
- `gender`: requerido, debe ser "male" o "female"
- `country`: requerido, máximo 255 caracteres

#### Actualizar un autor
```http
PUT /authors/{id}
Content-Type: application/json

{
  "name": "Isabel Allende Bussi",
  "country": "Estados Unidos"
}
```

#### Eliminar un autor
```http
DELETE /authors/{id}
```

### Libros (Books)

#### Obtener todos los libros
```http
GET /books
```

**Respuesta:**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Cien años de soledad",
      "description": "Novela del realismo mágico",
      "price": 2500,
      "author_id": 1,
      "created_at": "2020-11-24 16:59:11",
      "updated_at": "2020-11-24 16:59:11"
    }
  ]
}
```

#### Obtener un libro específico
```http
GET /books/{id}
```

#### Crear un libro
```http
POST /books
Content-Type: application/json

{
  "title": "La casa de los espíritus",
  "description": "Novela familiar",
  "price": 3000,
  "author_id": 1
}
```

**Validaciones:**
- `title`: requerido, máximo 255 caracteres
- `description`: requerido, máximo 255 caracteres
- `price`: requerido, mínimo 1
- `author_id`: requerido, mínimo 1 (debe existir en el servicio de autores)

#### Actualizar un libro
```http
PUT /books/{id}
Content-Type: application/json

{
  "title": "La casa de los espíritus (Edición especial)",
  "price": 3500
}
```

> **Nota**: Si se proporciona `author_id`, el Gateway valida que el autor exista antes de actualizar.

#### Eliminar un libro
```http
DELETE /books/{id}
```

## 🔍 Ejemplos de Uso

### Ejemplo completo: Crear un autor y sus libros

```bash
# 1. Crear un autor
curl -X POST http://localhost:8000/authors \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Mario Vargas Llosa",
    "gender": "male",
    "country": "Perú"
  }'

# Respuesta: {"data":{"id":1,"name":"Mario Vargas Llosa",...}}

# 2. Crear un libro del autor
curl -X POST http://localhost:8000/books \
  -H "Content-Type: application/json" \
  -d '{
    "title": "La ciudad y los perros",
    "description": "Primera novela del autor",
    "price": 2800,
    "author_id": 1
  }'

# 3. Obtener todos los libros
curl http://localhost:8000/books

# 4. Obtener un autor con sus datos
curl http://localhost:8000/authors/1
```

## 🛠️ Tecnologías Utilizadas

### Backend
- **Laravel Lumen 10.x**: Framework PHP ligero para APIs (actualizado desde 5.7)
- **PHP 8.2+**: Versión moderna de PHP con mejor rendimiento
- **Guzzle HTTP 7.8**: Cliente HTTP para comunicación entre servicios
- **Eloquent ORM**: ORM de Laravel para acceso a datos
- **SQLite**: Base de datos ligera para desarrollo

### Testing y CI/CD
- **PHPUnit 10.x**: Framework de testing para PHP
- **GitHub Actions**: Pipeline de CI/CD para GitHub
- **GitLab CI**: Pipeline de CI/CD para GitLab

### Documentación
- **OpenAPI 3.0**: Especificación estándar para documentación de APIs
- **Swagger UI**: Interfaz interactiva para visualizar y probar APIs
- **Mermaid**: Diagramas de arquitectura y flujos

## 📝 Estructura de Datos

### Tabla: authors
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INTEGER | Clave primaria |
| name | VARCHAR(255) | Nombre del autor |
| gender | VARCHAR(255) | Género (male/female) |
| country | VARCHAR(255) | País de origen |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de actualización |

### Tabla: books
| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INTEGER | Clave primaria |
| title | VARCHAR(255) | Título del libro |
| description | VARCHAR(255) | Descripción |
| price | INTEGER | Precio |
| author_id | INTEGER | ID del autor (referencia) |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de actualización |

## 🔐 Seguridad

> **Nota de Seguridad**: Este proyecto es una implementación educativa. Para producción, se recomienda:

- Implementar autenticación (JWT, OAuth2)
- Usar HTTPS
- Implementar rate limiting
- Validar y sanitizar todas las entradas
- Usar variables de entorno para secretos
- Implementar logging y monitoreo
- Agregar tests automatizados

## 🧪 Testing

### Tests Automatizados con PHPUnit

Para ejecutar los tests unitarios:

```bash
# En cada servicio
phpunit
```

### Scripts de Prueba Manual

El proyecto incluye scripts de prueba para verificar el funcionamiento del Gateway y los microservicios:

#### Prueba Simple del Gateway
```bash
bash test_gateway_simple.sh
```

Este script realiza pruebas básicas de conectividad:
- GET /authors
- GET /books

#### Prueba Completa del Gateway
```bash
bash test_gateway.sh
```

Este script realiza pruebas exhaustivas de todas las operaciones CRUD:
- ✅ Operaciones GET (listar y obtener recursos)
- ✅ Operaciones POST (crear recursos)
- ✅ Operaciones PUT (actualizar recursos)
- ✅ Validación de relaciones (autor inexistente)
- ✅ Manejo de errores (404, etc.)

#### Prueba de Todos los Servicios
```bash
bash test_api.sh
```

Este script prueba directamente los microservicios (sin pasar por el Gateway):
- Authors Service (puerto 8001)
- Books Service (puerto 8002)
- Gateway API (puerto 8000)

> **Nota**: Asegúrate de que todos los servicios estén corriendo antes de ejecutar los scripts de prueba.

## 📄 Licencia

Este proyecto utiliza la licencia MIT.

## 👥 Contribuciones

Este es un proyecto educativo para demostrar arquitectura de microservicios con Lumen.

## 🔄 CI/CD Pipeline

Este proyecto incluye pipelines de CI/CD configurados para GitHub Actions y GitLab CI:

- **GitHub Actions**: `.github/workflows/ci.yml`
- **GitLab CI**: `.gitlab-ci.yml`

**Características del Pipeline:**
- ✅ Validación de código PHP y sintaxis
- ✅ Instalación y validación de dependencias
- ✅ Ejecución de tests unitarios (PHPUnit)
- ✅ Tests de integración entre servicios
- ✅ Validación de documentación OpenAPI
- ✅ Reportes de cobertura de código

**Ejecutar validaciones localmente:**
```bash
bash pipeline/local-test.sh
```

Para más información, consulta la [Documentación del Pipeline](pipeline/README.md).

## 📚 Documentación Adicional

Este proyecto incluye documentación adicional para facilitar su uso y comprensión:

- **[arquitectura.md](arquitectura.md)**: Documentación detallada de la arquitectura con diagramas Mermaid interactivos
- **[guiaEstudiante.md](guiaEstudiante.md)**: Guía completa paso a paso para estudiantes que quieran crear nuevos microservicios y consumir servicios existentes

- **[Documentación de APIs](docs/index.html)**: Documentación interactiva OpenAPI/Swagger de todas las APIs (visualizable en el navegador)
- **[Documentación del Pipeline](pipeline/README.md)**: Guía completa sobre los pipelines CI/CD

### 📖 Documentación de APIs

La documentación completa de todas las APIs está disponible en formato OpenAPI/Swagger y puede visualizarse directamente en el navegador:

- **📄 [Ver Documentación Completa](docs/index.html)** - Página principal con índice de todas las APIs
- **🚪 [API Gateway](docs/api-gateway.html)** - Documentación del Gateway (recomendado)
- **👤 [Authors API](docs/api-authors.html)** - Documentación del servicio de Authors
- **📖 [Books API](docs/api-books.html)** - Documentación del servicio de Books

**Características:**
- ✅ Interfaz interactiva con Swagger UI
- ✅ Prueba endpoints directamente desde el navegador
- ✅ Especificación OpenAPI 3.0 estándar
- ✅ Importable en Postman, Insomnia y otras herramientas

Para abrir la documentación, simplemente abre `docs/index.html` en tu navegador o ejecuta un servidor HTTP local desde la carpeta `docs`.

### 🎓 Para Estudiantes

Si eres estudiante y quieres aprender a crear nuevos microservicios, consulta la **[Guía del Estudiante](guiaEstudiante.md)** que incluye:

- ✅ Cómo crear un nuevo microservicio desde cero
- ✅ Cómo integrarlo con el API Gateway
- ✅ Cómo consumir otros servicios (Authors y Books) desde tu nuevo servicio
- ✅ Ejemplo completo: Servicio de Reviews
- ✅ Pruebas y validación
- ✅ Mejores prácticas y ejercicios prácticos

## 📞 Soporte

Para preguntas o problemas, revisa la documentación de [Laravel Lumen](https://lumen.laravel.com/docs).

---

**Desarrollado con ❤️ usando Laravel Lumen** - xavicrip
