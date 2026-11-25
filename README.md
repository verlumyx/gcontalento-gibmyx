# Gestión de Biblioteca de Libros (Laravel + PHP)

Aplicación funcional para la gestión de una biblioteca de libros y categorías mediante **API REST en Laravel** y un **panel web** construido con Blade, Bootstrap 5, jQuery y AJAX.

---

## Requisitos

### Instalación estándar (Laravel local)
- **PHP** >= 8.3 (con extensiones `pdo`, `pdo_sqlite` o `pdo_mysql`/`pdo_pgsql`, `mbstring`, `xml`, `bcmath`, `intl`, `zip`, `gd`)
- **Composer**
- Base de datos: **SQLite** (configurada por defecto en `.env.example`) o **MySQL / PostgreSQL**

### Instalación con Docker
- **Docker** y **Docker Compose**
- **Make** (opcional, incluye atajos en `Makefile`)

---

## 1. Instalación normal de Laravel (sin Docker)

1. **Clonar el repositorio y entrar al directorio del proyecto:**
   ```bash
   git clone https://github.com/verlumyx/gcontalento-gibmyx.git
   # o por SSH:
   # git clone git@github.com:verlumyx/gcontalento-gibmyx.git
   cd gcontalento-gibmyx
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Crear el archivo de entorno y generar la clave de aplicación:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configurar la base de datos:**
   - **Opción A — SQLite (por defecto en `.env.example`):**
     ```bash
     touch database/database.sqlite
     ```
   - **Opción B — MySQL / PostgreSQL:** edita `.env` con tus credenciales:
     ```env
     DB_CONNECTION=mysql # o pgsql
     DB_HOST=127.0.0.1
     DB_PORT=3306        # o 5432 en PostgreSQL
     DB_DATABASE=biblioteca
     DB_USERNAME=tu_usuario
     DB_PASSWORD=tu_password
     ```

5. **Ejecutar migraciones y seeders:**
   ```bash
   php artisan migrate --seed
   ```
   *(Si deseas reiniciar la base de datos desde cero con los datos iniciales: `php artisan migrate:fresh --seed`.)*

6. **Iniciar el servidor local de Laravel:**
   ```bash
   php artisan serve
   ```

7. **Accesos locales:**
   - **Inicio / Panel Web:** http://127.0.0.1:8000/
   - **Gestión de Libros (Web):** http://127.0.0.1:8000/panel/books
   - **Gestión de Categorías (Web):** http://127.0.0.1:8000/panel/categories
   - **Documentación interactiva de la API:** http://127.0.0.1:8000/docs/api

> Los assets del panel web (Bootstrap 5, Bootstrap Icons y jQuery) están incluidos localmente en `public/vendor/` y `public/js/`, por lo que no necesitas ejecutar `npm run build` para usar la aplicación.

---

## 2. Instalación con Docker

El proyecto incluye `Dockerfile` (PHP 8.5-FPM), `docker-compose.yml` (servicios `app` + `nginx`) y `Makefile`. Al arrancar el contenedor (`docker/entrypoint.sh`), crea automáticamente `.env` (si no existe), inicializa `database/database.sqlite`, genera `APP_KEY` y ejecuta `php artisan migrate --force`.

1. **Clonar el repositorio y entrar al directorio:**
   ```bash
   git clone https://github.com/verlumyx/gcontalento-gibmyx.git
   # o por SSH:
   # git clone git@github.com:verlumyx/gcontalento-gibmyx.git
   cd gcontalento-gibmyx
   ```

2. **(Opcional) Copiar `.env.example` a `.env`:**
   ```bash
   cp .env.example .env
   ```

3. **Construir y levantar los contenedores:**
   ```bash
   make dev
   # o directamente con Docker Compose:
   docker compose up -d --build
   ```

4. **Cargar los datos iniciales (Seeders):**
   ```bash
   docker compose exec app php artisan db:seed
   # o para recrear tablas + seeders desde cero:
   docker compose exec app php artisan migrate:fresh --seed
   ```

5. **Accesos en Docker:**
   - **Inicio / Panel Web:** http://localhost:8080/
   - **Gestión de Libros (Web):** http://localhost:8080/panel/books
   - **Gestión de Categorías (Web):** http://localhost:8080/panel/categories
   - **Documentación interactiva de la API:** http://localhost:8080/docs/api

### Comandos útiles con Docker (`Makefile`)

| Comando | Equivalente Docker | Descripción |
| :--- | :--- | :--- |
| `make dev` | `docker compose up -d --build` | Construye y levanta los contenedores |
| `make down` | `docker compose down` | Detiene los contenedores |
| `make migrate` | `docker compose exec app php artisan migrate --force` | Ejecuta migraciones pendientes |
| `make test` | `docker compose exec ... app php artisan test` | Ejecuta toda la suite de pruebas |
| `make app-shell` | `docker compose exec app sh` | Abre una terminal dentro del contenedor PHP |
| `make logs` | `docker compose logs -f` | Muestra logs en tiempo real |

---

## 3. Dónde está la documentación de la API

La documentación OpenAPI 3.1 se genera automáticamente con **Dedoc Scramble** (`config/scramble.php`) a partir de los controladores, Form Requests y API Resources:

- **Interfaz interactiva (Swagger / Elements UI):**
  - Local (`php artisan serve`): http://127.0.0.1:8000/docs/api
  - Docker: http://localhost:8080/docs/api
- **Especificación OpenAPI en JSON:**
  - Local (`php artisan serve`): http://127.0.0.1:8000/docs/api.json
  - Docker: http://localhost:8080/docs/api.json

> También puedes acceder directamente desde el botón **"API Docs"** en la barra de navegación superior del panel web. En entorno `local` está habilitada sin restricciones; en `production` se controla mediante el gate `viewApiDocs`.

---

## 4. Rutas del Panel Web (Vistas Blade + Bootstrap 5 + jQuery/AJAX)

Para evitar colisiones con los endpoints JSON de la API (`/books` y `/categories`), todas las vistas de gestión web están agrupadas bajo `/panel`:

| Pantalla | Ruta (Docker `8080`) | Ruta (`php artisan serve` `8000`) | Descripción |
| :--- | :--- | :--- | :--- |
| Inicio | http://localhost:8080/ | http://127.0.0.1:8000/ | Portada con accesos a Libros, Categorías y API Docs |
| Listado de libros | http://localhost:8080/panel/books | http://127.0.0.1:8000/panel/books | Listado, filtro por categoría y eliminación |
| Crear libro | http://localhost:8080/panel/books/create | http://127.0.0.1:8000/panel/books/create | Formulario AJAX conectado a `POST /books` |
| Editar libro | `http://localhost:8080/panel/books/{id}/edit` | `http://127.0.0.1:8000/panel/books/{id}/edit` | Formulario AJAX conectado a `PUT /books/{id}` |
| Listado de categorías | http://localhost:8080/panel/categories | http://127.0.0.1:8000/panel/categories | Listado y eliminación (bloqueada si tiene libros) |
| Crear categoría | http://localhost:8080/panel/categories/create | http://127.0.0.1:8000/panel/categories/create | Formulario AJAX conectado a `POST /categories` |
| Editar categoría | `http://localhost:8080/panel/categories/{id}/edit` | `http://127.0.0.1:8000/panel/categories/{id}/edit` | Formulario AJAX conectado a `PUT /categories/{id}` |

---

## 5. Endpoints de la API REST

Los endpoints de la API responden en JSON directamente en la raíz (sin prefijo `/api` ni validación CSRF) y están definidos en `routes/api.php` (configurado en `bootstrap/app.php` con `apiPrefix: ''`).

### Libros (`/books`)

| Método | Endpoint | Descripción | Códigos HTTP |
| :--- | :--- | :--- | :--- |
| `GET` | `/books` | Lista todos los libros con su categoría. | `200` |
| `GET` | `/books?category={category_id}` | Filtra los libros por ID de categoría. | `200`, `422` |
| `GET` | `/books/{id}` | Obtiene el detalle de un libro específico. | `200`, `404` |
| `POST` | `/books` | Crea un nuevo libro. | `201`, `422` |
| `PUT` / `PATCH` | `/books/{id}` | Actualiza la información de un libro existente. | `200`, `404`, `422` |
| `DELETE` | `/books/{id}` | Verifica la existencia del libro y lo elimina. | `200`, `404` |

### Categorías (`/categories`)

| Método | Endpoint | Descripción | Códigos HTTP |
| :--- | :--- | :--- | :--- |
| `GET` | `/categories` | Lista todas las categorías ordenadas por ID. | `200` |
| `GET` | `/categories/{id}` | Obtiene el detalle de una categoría específica. | `200`, `404` |
| `POST` | `/categories` | Crea una categoría validando nombre único. | `201`, `422` |
| `PUT` / `PATCH` | `/categories/{id}` | Actualiza una categoría existente. | `200`, `404`, `422` |
| `DELETE` | `/categories/{id}` | Elimina una categoría si existe y no tiene libros asociados. | `200`, `404`, `409` |

### Ejemplos de uso con `curl`

*(Ajusta `http://localhost:8080` por `http://127.0.0.1:8000` si usas `php artisan serve`.)*

```bash
# Listar todos los libros
curl -s http://localhost:8080/books

# Filtrar libros por categoría
curl -s "http://localhost:8080/books?category=1"

# Crear una categoría
curl -s -X POST http://localhost:8080/categories \
  -H "Content-Type: application/json" \
  -d '{"name": "Poesía"}'

# Crear un libro
curl -s -X POST http://localhost:8080/books \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Veinte poemas de amor",
    "author": "Pablo Neruda",
    "description": "Poemario clásico.",
    "published_year": 1924,
    "category_id": 1
  }'

# Actualizar un libro
curl -s -X PUT http://localhost:8080/books/1 \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Cien años de soledad",
    "author": "Gabriel García Márquez",
    "description": "Edición revisada.",
    "published_year": 1967,
    "category_id": 1
  }'

# Eliminar un libro
curl -s -X DELETE http://localhost:8080/books/1
```

---

## 6. Modelo de Datos, Validaciones y Lógica de Negocio

- **Migraciones (`database/migrations/`):**
  - `categories`: `id`, `name` (`string`, `unique`), `timestamps`.
  - `books`: `id`, `title`, `author`, `description` (`text`, `nullable`), `published_year` (`integer`), `category_id` (`foreignId` → `categories.id` con `restrictOnDelete()`), `timestamps`.
- **Modelos Eloquent (`app/Models/`):**
  - `Category` (`hasMany(Book::class)`) y `Book` (`belongsTo(Category::class)`).
- **Seeders (`database/seeders/`):**
  - `CategorySeeder` (5 categorías) y `BookSeeder` (7 libros), ejecutados desde `DatabaseSeeder`.
- **Form Requests (`app/Http/Requests/`):**
  - `IndexBooksRequest`, `CreateBookRequest`, `UpdateBookRequest` (`BookRequest`), `CreateCategoryRequest`, `UpdateCategoryRequest`.
  - Reglas: `title` obligatorio mín. 3 caracteres, `author` obligatorio, `published_year` entero entre `1000` y el año actual, `category_id` existente en `categories`, y `name` único en categorías.
- **Lógica de negocio:**
  - Verificación explícita de existencia antes de eliminar un libro (`404 Not Found`).
  - Verificación de libros asociados antes de eliminar una categoría (`409 Conflict` si tiene libros).

---

## 7. Pruebas automatizadas

- **Instalación normal (sin Docker):**
  ```bash
  php artisan test
  ```
- **Con Docker:**
  ```bash
  make test
  ```
