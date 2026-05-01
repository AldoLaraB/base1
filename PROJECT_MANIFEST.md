# PROJECT_MANIFEST.md

## 1. Panoramica del Progetto

| Campo            | Valore                                                                                                      |
| ---------------- | ----------------------------------------------------------------------------------------------------------- |
| **Nome**         | Laravel Base Application                                                                                    |
| **Tipo**         | Web Application (Laravel 12)                                                                                |
| **Descrizione**  | Skeleton application per Laravel framework con sistema di gestione media (immagini) e autenticazione utente |
| **Licenza**      | MIT                                                                                                         |
| **Versione PHP** | ^8.2                                                                                                        |

---

## 2. Stack Tecnologico

### Backend

- **Framework**: Laravel 12.0
- **PHP**: ^8.2
- **Database**: SQLite (default), supporta MySQL/PostgreSQL
- **Image Processing**: intervention/image-laravel ^1.5

### Frontend

- **Build Tool**: Vite 7.0.7
- **CSS Framework**: Tailwind CSS 3.1.0
- **JavaScript**: Alpine.js 3.4.2, Axios 1.11.0
- **Forms**: @tailwindcss/forms 0.5.2

### Development Tools

- **Testing**: Pest PHP 4.3 + pest-plugin-laravel 4.0
- **Code Style**: Laravel Pint 1.24
- **Scaffolding**: Laravel Breeze 2.3 (authentication)
- **Dev Server**: Laravel Sail 1.41

---

## 3. Struttura del Database

### Tabella `users`

| Colonna           | Tipo      | Note                                   |
| ----------------- | --------- | -------------------------------------- |
| id                | bigint    | Primary key                            |
| name              | string    | Nome utente                            |
| email             | string    | Email univoca                          |
| email_verified_at | timestamp | Data verifica email                    |
| password          | string    | Password hashed                        |
| remember_token    | string    | Token remember                         |
| is_active         | boolean   | Stato attivo/disattivo (default: true) |
| created_at        | timestamp |                                        |
| updated_at        | timestamp |                                        |

> **Nota**: Il campo `role` è stato rimosso. La gestione ruoli è ora gestita da Spatie Permission.

### Tabella `media`

| Colonna    | Tipo            | Note                                  |
| ---------- | --------------- | ------------------------------------- |
| id         | bigint          | Primary key                           |
| model_id   | bigint          | ID del modello associato (morph)      |
| model_type | string          | Tipo modello (es. App\Models\User)    |
| collection | string          | Collezione (avatar, gallery, default) |
| disk       | string          | Disk storage (default: 'public')      |
| path       | string          | Percorso file                         |
| filename   | string          | Nome file                             |
| mime_type  | string          | Tipo MIME                             |
| size       | unsignedInteger | Dimensione file                       |
| width      | unsignedInteger | Larghezza immagine                    |
| height     | unsignedInteger | Altezza immagine                      |
| order      | unsignedInteger | Ordinamento                           |
| is_primary | boolean         | Immagine primaria                     |
| created_at | timestamp       |                                       |
| updated_at | timestamp       |                                       |

---

## 4. Modelli (Eloquent)

### User Model

**Path**: `app/Models/User.php`

- Estende `Authenticatable`
- Usa trait `HasMedia`
- Campi fillable: `name`, `email`, `password`, `role`, `is_active`
- Metodo `isAdmin()`: verifica se ruolo = 'admin'
- Evento `deleting`: cancella avatar e gallery alla rimozione utente

### Media Model

**Path**: `app/Models/Media.php`

- Rappresenta file media (immagini)
- Relazione morphTo `model()` per associazione polimorfica
- Accessor `url`: URL completo del media
- Accessor `fullPath`: percorso fisico del file

---

## 5. Trait

### HasMedia Trait

**Path**: `app/Traits/HasMedia.php`

Metodi disponibili:

- `media()`: relazione morphMany
- `primaryMedia($collection)`: ottiene media primario
- `getMediaByCollection($collection)`: ottiene tutti i media di una collezione
- `addMedia($file, $collection, $isPrimary)`: aggiunge nuovo media con validazione

Validazione file:

- MIME consentiti: `image/jpeg`, `image/png`, `image/gif`, `image/webp`
- Estensioni: `jpg`, `jpeg`, `png`, `gif`, `webp`

---

## 6. Controller

### ProfileController

**Path**: `app/Http/Controllers/ProfileController.php`

- `edit(Request)`: mostra form modifica profilo
- `update(ProfileUpdateRequest)`: aggiorna dati profilo
- `destroy(Request)`: elimina account utente

### AvatarController

**Path**: `app/Http/Controllers/Profile/AvatarController.php`

- `edit()`: mostra form modifica avatar
- `update(Request)`: carica nuovo avatar
- `destroy()`: elimina avatar

---

## 7. Request Validation

### ProfileUpdateRequest

**Path**: `app/Http/Requests/ProfileUpdateRequest.php`

- `name`: required, string, max:255
- `email`: required, string, lowercase, email, max:255, unique (ignore current user)

---

## 8. Route

### web.php

**Path**: `routes/web.php`

| Metodo | URI             | Controller                | Middleware     | Nome rotta             |
| ------ | --------------- | ------------------------- | -------------- | ---------------------- |
| GET    | /               | closure                   | -              | -                      |
| GET    | /dashboard      | closure                   | auth, verified | dashboard              |
| GET    | /profile        | ProfileController@edit    | auth           | profile.edit           |
| PATCH  | /profile        | ProfileController@update  | auth           | profile.update         |
| DELETE | /profile        | ProfileController@destroy | auth           | profile.destroy        |
| GET    | /profile/avatar | AvatarController@edit     | auth           | profile.avatar         |
| PUT    | /profile/avatar | AvatarController@update   | auth           | profile.avatar.update  |
| DELETE | /profile/avatar | AvatarController@destroy  | auth           | profile.avatar.destroy |

Rotte auth.php (Breeze):

- Login, Register, Logout, Password Reset, Email Verification

---

## 9. View

### Blade Templates

**Path**: `resources/views/`

| File                                                       | Descrizione               |
| ---------------------------------------------------------- | ------------------------- |
| welcome.blade.php                                          | Pagina iniziale           |
| dashboard.blade.php                                        | Dashboard utente loggato  |
| profile/edit.blade.php                                     | Pagina modifica profilo   |
| profile/avatar.blade.php                                   | Pagina modifica avatar    |
| profile/partials/update-profile-information-form.blade.php | Form info profilo         |
| profile/partials/update-password-form.blade.php            | Form cambio password      |
| profile/partials/delete-user-form.blade.php                | Form eliminazione account |

Layout: usa componenti Blade Laravel (x-app-layout, x-slot)

---

## 10. Seeder

### AdminUserSeeder

**Path**: `database/seeders/AdminUserSeeder.php`

Crea utente amministratore:

- **Email**: admin@example.com
- **Password**: password123
- **Nome**: Amministratore
- **Ruolo**: admin
- **is_active**: true

---

## 11. Configurazione

### config/app.php

- Nome: Laravel (default)
- Environment: production (default)
- Debug: false (default)
- URL: http://localhost

### config/database.php

- Default: SQLite
- Supporta: mysql, pgsql, sqlsrv

### config/auth.php

- Guard: web (session)
- Provider: users (eloquent)
- Password broker: users

---

## 12. Dipendenze Composer

### require

```json
{
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "laravel/tinker": "^2.10.1",
    "intervention/image-laravel": "^1.5",
    "spatie/laravel-permission": "^7.0",
    "laravel/sanctum": "^4.0"
}
```

### require-dev

```json
{
    "fakerphp/faker": "^1.23",
    "laravel/breeze": "^2.3",
    "laravel/pail": "^1.2.2",
    "laravel/pint": "^1.24",
    "laravel/sail": "^1.41",
    "mockery/mockery": "^1.6",
    "nunomaduro/collision": "^8.6",
    "pestphp/pest": "^4.3",
    "pestphp/pest-plugin-laravel": "^4.0"
}
```

---

## 13. Dipendenze npm

### devDependencies

```json
{
    "@tailwindcss/forms": "^0.5.2",
    "@tailwindcss/vite": "^4.0.0",
    "alpinejs": "^3.4.2",
    "autoprefixer": "^10.4.2",
    "axios": "^1.11.0",
    "concurrently": "^9.0.1",
    "laravel-vite-plugin": "^2.0.0",
    "postcss": "^8.4.31",
    "tailwindcss": "^3.1.0",
    "vite": "^7.0.7"
}
```

---

## 14. Script npm

| Script | Comando    | Descrizione        |
| ------ | ---------- | ------------------ |
| build  | vite build | Build produzione   |
| dev    | vite       | Development server |

---

## 15. Script Composer

| Script                    | Comando        | Descrizione             |
| ------------------------- | -------------- | ----------------------- |
| setup                     | composer setup | Setup completo progetto |
| dev                       | (various)      | Development commands    |
| post-root-package-install | (scripts)      | Post install            |
| post-create-project-cmd   | (scripts)      | Post create project     |

---

## 16. Test

### Feature Tests

- `tests/Feature/ExampleTest.php`
- `tests/Feature/ProfileTest.php`
- `tests/Feature/Auth/`

### Unit Tests

- `tests/Unit/ExampleTest.php`

Framework: Pest PHP

---

## 17. Struttura File Principali

```
base1/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Profile/
│   │   │   │   └── AvatarController.php
│   │   │   └── ProfileController.php
│   │   └── Requests/
│   │       └── ProfileUpdateRequest.php
│   ├── Models/
│   │   ├── Media.php
│   │   └── User.php
│   ├── Traits/
│   │   └── HasMedia.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   ├── app.php
│   └── providers.php
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   └── (altri config)
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   └── 2026_02_16_105304_create_media_table.php│   │   ├── 2026_04_30_000000_create_permission_tables.php
│   │   └── 2026_04_30_000001_drop_users_role_column.php│   └── seeders/
│       ├── AdminUserSeeder.php
│       └── DatabaseSeeder.php
├── public/
│   ├── index.php
│   └── (altri file pubblici)
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views/
│       ├── dashboard.blade.php
│       ├── welcome.blade.php
│       └── profile/
│           ├── edit.blade.php
│           ├── avatar.blade.php
│           └── partials/
├── routes/
│   ├── web.php
│   ├── auth.php
│   └── console.php
├── tests/
│   ├── Feature/
│   │   ├── ExampleTest.php
│   │   └── ProfileTest.php
│   └── Unit/
│       └── ExampleTest.php
├── composer.json
├── package.json
├── vite.config.js
├── tailwind.config.js
└── phpunit.xml
```

---

## 18. Note di Implementazione

### Sistema Media

- Il sistema media è implementato con pattern **Morph** (polimorfico)
- Un singolo modello `Media` può essere associato a qualsiasi entità (User, Product, Post, ecc.)
- La tabella `media` usa `model_id` + `model_type` per le relazioni polimorfiche
- Il trait `HasMedia` fornisce metodi utility per gestire upload, cancellazione e recupero media

### Gestione Avatar

- Gli avatar sono memorizzati nella collection 'avatar'
- Un utente può avere un solo avatar primario (`is_primary = true`)
- Alla cancellazione dell'utente, avatar e gallery vengono eliminati automaticamente

### Autenticazione

- Usa **Laravel Breeze** per autenticazione completa
- Include: login, registrazione, logout, reset password, verifica email
- Middleware `auth` e `verified` per protezione route

### Ruoli Utente

- Campo `role`: 'user' (default), 'admin'
- Campo `is_active`: boolean per attivare/disattivare utenti
- Metodo `isAdmin()` per verifica rapida

---

## 19. Comandi Utili

```bash
# Setup progetto
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev

# Seed database
php artisan db:seed --class=AdminUserSeeder

# Test
./vendor/bin/pest

# Code style
./vendor/bin/pint
```

---

## 20. Credenziali Default

| Campo          | Valore            |
| -------------- | ----------------- |
| Email Admin    | admin@example.com |
| Password Admin | password123       |

---

## 21. Sistema Ruoli e Permessi (Spatie)

### Tabelle Spatie Permission

| Tabella                 | Descrizione                  |
| ----------------------- | ---------------------------- |
| `permissions`           | Permessi singoli             |
| `roles`                 | Ruoli utente                 |
| `model_has_permissions` | Permessi assegnati a modelli |
| `model_has_roles`       | Ruoli assegnati a modelli    |
| `role_has_permissions`  | Relazione ruoli-permessi     |

### Ruoli Creati

| Ruolo    | Permessi                                             |
| -------- | ---------------------------------------------------- |
| `admin`  | Tutti i permessi                                     |
| `editor` | profile.view, profile.edit, media.view, media.upload |
| `user`   | profile.view, profile.edit                           |

### Permessi Creati

| Permesso       | Descrizione        |
| -------------- | ------------------ |
| `user.view`    | Visualizza utenti  |
| `user.create`  | Crea utenti        |
| `user.edit`    | Modifica utenti    |
| `user.delete`  | Elimina utenti     |
| `profile.view` | Visualizza profilo |
| `profile.edit` | Modifica profilo   |
| `media.view`   | Visualizza media   |
| `media.upload` | Carica media       |
| `media.delete` | Elimina media      |

### Seeder

- `database/seeders/RolePermissionSeeder.php` - Crea ruoli e permessi base

### Metodi Helper (HasRoles Trait)

- `$user->hasRole('admin')` - Verifica ruolo
- `$user->assignRole('admin')` - Assegna ruolo
- `$user->removeRole('admin')` - Rimuove ruolo
- `$user->givePermissionTo('user.create')` - Assegna permesso
- `$user->can('user.edit')` - Verifica permesso

---

## 22. API REST (Laravel Sanctum)

### Endpoint API

| Metodo | URI           | Controller              | Autenticazione | Descrizione                |
| ------ | ------------- | ----------------------- | -------------- | -------------------------- |
| POST   | /api/login    | AuthController@login    | No             | Login e ottenimento token  |
| POST   | /api/register | AuthController@register | No             | Registrazione nuovo utente |
| GET    | /api/me       | AuthController@me       | Sanctum        | Info utente corrente       |
| POST   | /api/logout   | AuthController@logout   | Sanctum        | Revoca token               |
| GET    | /api/user     | -                       | Sanctum        | User (default Laravel)     |

### Tabella Token

- `personal_access_tokens` - Token API utente

### Controller

- `app/Http/Controllers/Api/AuthController.php`

### Configurazione

- `config/sanctum.php`

### Trait User

- `Laravel\Sanctum\HasApiTokens` - Gestione token API

### Metodi Helper

- `$user->createToken('name')` - Crea token
- `$user->tokens` - Relazione token
- `$user->currentAccessToken()` - Token corrente

---

_Documento generato automaticamente il 1 maggio 2026_
