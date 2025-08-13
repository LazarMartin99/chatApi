# Laravel Chat API

Egyszerű REST API chat alkalmazáshoz Laravel 11-ben.

## Gyors telepítés

### 1. Projekt letöltése

```bash
git clone <repository-url> chatapi
cd chatapi
```

### 2. Alapbeállítások

```bash
composer install
cp .env.example .env
php artisan key:generate
```

### 3. Adatbázis

```bash
php artisan migrate
```

### 4. Laravel Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### 5. Indítás

```bash
php artisan serve
```

API elérhető: `http://localhost:8000`

## .env konfiguráció

Alapértelmezett beállítások:

```env

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@chatapi.local"
MAIL_FROM_NAME="Chat API"
```

## Tesztelés

```bash
# Tesztek futtatása
php artisan test --filter=ChatApiTest

```

## API végpontok

### Publikus

-   `POST /api/register` - Regisztráció
-   `POST /api/login` - Bejelentkezés
-   `GET /api/email/verify/{id}/{hash}` - Email verifikáció

### Autentikált (Bearer token szükséges)

-   `GET /api/me` - Profil
-   `GET /api/users` - Felhasználók listája
-   `POST /api/friends/add` - Ismerős hozzáadása
-   `GET /api/friends` - Ismerősök listája
-   `POST /api/messages/send` - Üzenet küldése
-   `GET /api/messages/conversation/{userId}` - Beszélgetés

## Használat

1. **Regisztráció** → Email verifikáció (link a `storage/logs/laravel.log`-ban)
2. **Bejelentkezés** → Token visszakapása
3. **Token használata** az Authorization headerben: `Bearer YOUR_TOKEN`

## Követelmények

-   PHP 8.3+
-   Composer
-   SQLite vagy MySQL
