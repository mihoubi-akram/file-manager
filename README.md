# File Manager

A secure file management system where authenticated users can upload, list, download, and delete their own files.

## Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 13 (PHP 8.3) |
| Frontend | Vue 3 + TypeScript + Vite |
| Database | MySQL 8.4 |
| Auth | Laravel Sanctum |
| Containerization | Docker Compose (Laravel Sail) |

---

## Requirements

- Docker & Docker Compose

---

## Setup & Run

### 1. Clone the repository

```bash
git clone <repository-url>
cd file-manager
```

### 2. Configure environment

```bash
cd backend
cp .env.example .env
```

Open `.env` and set the database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password
```

### 3. Start containers

```bash
docker compose up -d
```

This starts three services:
- `laravel.test` — Laravel API on port `80`
- `frontend` — Vue 3 dev server on port `5173`
- `mysql` — MySQL 8.4 on port `3306`

### 4. Install dependencies & initialize the app

```bash
docker compose exec laravel.test composer install
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate
```

### 5. Open the app

- Frontend: http://localhost:5173
- API: http://localhost

---

## Running Tests

```bash
docker compose exec laravel.test php artisan test
```