# Sistema de Torneos

Aplicación web para la gestión integral de torneos deportivos multi-disciplina. Permite administrar torneos, disciplinas, categorías, jugadores, participantes, partidos, resultados y tablas de posiciones.

## Stack Tecnológico

| Capa | Tecnología |
| ---- | ---------- |
| Framework | Laravel 12 (PHP 8.2+) |
| Frontend reactivo | Livewire 3 + Volt |
| Estilos | Tailwind CSS + PostCSS |
| Build | Vite |
| Base de datos | SQLite (por defecto) / MySQL o PostgreSQL |
| Permisos | Spatie Laravel Permission 6 |
| Autenticación | Laravel Breeze (Livewire stack) |

---

## Arquitectura

El proyecto sigue una estructura de **Domain-Driven Design (DDD)** dentro del directorio `app/Domains/`, con tres dominios principales:

```text
app/
├── Domains/
│   ├── Tournaments/           # Gestión del torneo en sí
│   │   └── Domain/Models/
│   │       ├── Tournament.php
│   │       ├── Discipline.php
│   │       └── Category.php
│   ├── Registration/          # Inscripción de jugadores y participantes
│   │   └── Domain/Models/
│   │       ├── Player.php
│   │       ├── Participant.php
│   │       └── CategoryRegistration.php
│   └── Competition/           # Lógica de competencia (partidos, resultados)
│       ├── Domain/Models/
│       │   ├── MatchModel.php
│       │   └── MatchResult.php
│       └── Application/Services/
│           ├── GenerateLeagueFixtureService.php
│           └── ComputeStandingsService.php
├── Livewire/                  # Componentes interactivos (UI)
│   ├── Tournaments/
│   ├── Disciplines/
│   ├── Categories/
│   ├── Players/
│   ├── Participants/
│   ├── Matches/
│   ├── Standings/
│   └── Brackets/
└── Models/
    └── User.php
```

---

## Modelo de Datos

### Jerarquía principal

```text
Tournament
  └── Discipline (ej: Fútbol, Pádel)
        └── Category (ej: Masculino, Femenino, Mixto)
              ├── CategoryRegistration ──► Participant
              │                                └── Player (pivot: role)
              └── Match
                    ├── home_participant → Participant
                    ├── away_participant → Participant
                    └── MatchResult (scores, details)
```

### Entidades

#### `Tournament`

| Campo | Tipo | Descripción |
| ----- | ---- | ----------- |
| `id` | ULID | PK |
| `name` | string | Nombre del torneo |
| `season` | string | Temporada (ej: "2026", "Apertura 2026") |
| `starts_on` | date | Fecha de inicio |
| `ends_on` | date | Fecha de fin |
| `status` | string | `draft` \| `published` \| `running` \| `finished` |

#### `Discipline`

| Campo | Tipo | Descripción |
| ----- | ---- | ----------- |
| `id` | ULID | PK |
| `tournament_id` | ULID | FK → Tournament |
| `name` | string | Nombre (ej: Football, Pádel) |
| `config` | JSON | Configuración específica del deporte |

#### `Category`

| Campo | Tipo | Descripción |
| ----- | ---- | ----------- |
| `id` | ULID | PK |
| `discipline_id` | ULID | FK → Discipline |
| `name` | string | Nombre (ej: Masculino, Primera) |
| `format` | string | `league` \| `knockout` |
| `team_size` | int | Jugadores por equipo/pareja |
| `min_players` / `max_players` | int | Rango de jugadores |
| `rules` | JSON | Reglas: puntos, desempates, sets, etc. |

#### `Player`

| Campo | Tipo | Descripción |
| ----- | ---- | ----------- |
| `id` | ULID | PK |
| `first_name`, `last_name` | string | Nombre |
| `document` | string | DNI/CI |
| `birthdate` | date | Fecha de nacimiento |
| `gender` | string | M / F / X |
| `phone`, `email` | string | Contacto |

#### `Participant`

Unidad competitiva (equipo o pareja). Un participant agrupa uno o más players.

| Campo | Tipo | Descripción |
| ----- | ---- | ----------- |
| `id` | ULID | PK |
| `type` | string | `team` \| `pair` |
| `name` | string | Nombre visible |
| `metadata` | JSON | Info extra (deporte, género, etc.) |

> La relación `Participant ↔ Player` es N:M con pivot `role` (`captain` \| `player`).

#### `CategoryRegistration`

| Campo | Tipo | Descripción |
| ----- | ---- | ----------- |
| `id` | ULID | PK |
| `category_id` | ULID | FK → Category |
| `participant_id` | ULID | FK → Participant |
| `status` | string | `active` \| `withdrawn` |
| `seed` | int | Cabeza de serie |
| `points_adjustment` | int | Ajuste manual de puntos |

#### `MatchModel` (tabla: `matches`)

| Campo | Tipo | Descripción |
| ----- | ---- | ----------- |
| `id` | ULID | PK |
| `category_id` | ULID | FK → Category |
| `round` | smallint | Jornada o ronda |
| `match_number` | smallint | Número de partido |
| `home_participant_id` | ULID | FK → Participant |
| `away_participant_id` | ULID | FK → Participant |
| `scheduled_at` | datetime | Fecha y hora programada |
| `venue` | string | Cancha/pista |
| `status` | string | `scheduled` \| `played` \| `canceled` \| `walkover` |

#### `MatchResult`

| Campo | Tipo | Descripción |
| ----- | ---- | ----------- |
| `id` | ULID | PK |
| `match_id` | ULID | FK → Match |
| `home_score` | int | Goles/sets local |
| `away_score` | int | Goles/sets visitante |
| `details` | JSON | Detalle por sets, notas, etc. |
| `registered_at` | datetime | Cuándo se cargó |

---

## Servicios de Dominio

### `GenerateLeagueFixtureService`

Genera el fixture completo de una liga usando el **algoritmo "circle method" (round-robin)**:

- Si el número de participantes es impar, agrega un BYE automático.
- Alterna la localía en rondas pares para reducir sesgo.
- Acepta múltiples canchas y espaciado en días entre jornadas.

```php
$service->generate(
    categoryId: $category->id,
    participantIds: [...],
    startAt: Carbon::now()->addDay(),
    daysBetweenRounds: 7,
    venues: ['Cancha Central', 'Cancha 2']
);
```

### `ComputeStandingsService`

Calcula la tabla de posiciones de liga estilo fútbol:

- Incluye todos los participantes activos (aunque no hayan jugado).
- Procesa solo partidos con `status = 'played'` y resultado registrado.
- Columnas: PJ, PG, PE, PP, GF, GC, DG, PTS.
- Criterios de desempate: puntos → diferencia de gol → goles a favor → nombre.

```php
$standings = $service->compute($category->id);
```

---

## Roles y Permisos

Gestionados con **Spatie Laravel Permission**. Cuatro roles predefinidos:

| Rol | Descripción |
| --- | ----------- |
| `admin` | Acceso total, incluyendo gestión de usuarios y roles |
| `organizer` | Gestión completa del torneo (excepto usuarios/roles) |
| `referee` | Solo puede ver partidos y cargar resultados |
| `viewer` | Solo lectura (torneos, partidos, standings, brackets) |

### Permisos disponibles

| Grupo | Permisos |
| ----- | -------- |
| Torneos | `tournaments.view`, `tournaments.create`, `tournaments.update`, `tournaments.delete` |
| Disciplinas/Categorías | `disciplines.manage`, `categories.manage` |
| Registro | `players.manage`, `participants.manage`, `registrations.manage` |
| Partidos | `matches.view`, `matches.schedule`, `matches.update`, `results.enter` |
| Resultados | `standings.view`, `brackets.view` |
| Administración | `users.manage`, `roles.manage` |

---

## Rutas

Todas las rutas (excepto `/` y auth) requieren autenticación (`middleware: auth`).

| Ruta | Componente Livewire | Descripción |
| ---- | ------------------- | ----------- |
| `GET /dashboard` | `Dashboard` | Panel principal |
| `GET /tournaments` | `Tournaments\Index` | Listado de torneos |
| `GET /tournaments/create` | `Tournaments\Form` | Crear torneo |
| `GET /disciplines` | `Disciplines\Manager` | Gestión de disciplinas |
| `GET /categories` | `Categories\Manager` | Gestión de categorías |
| `GET /players` | `Players\Index` | Listado de jugadores |
| `GET /participants` | `Participants\Index` | Listado de participantes |
| `GET /matches/calendar` | `Matches\Calendar` | Calendario de partidos |
| `GET /matches/scheduler` | `Matches\Scheduler` | Programador de partidos |
| `GET /matches/results` | `Matches\ResultEntry` | Carga de resultados |
| `GET /standings` | `Standings\Table` | Tabla de posiciones |
| `GET /brackets` | `Brackets\View` | Vista de llaves |
| `GET /profile` | *(blade)* | Perfil del usuario |

---

## Instalación y Configuración

### Requisitos

- PHP 8.2+
- Composer
- Node.js + npm

### Instalación rápida (script automático)

```bash
composer run setup
```

Este comando ejecuta en secuencia: `composer install`, copia `.env`, genera la app key, ejecuta migraciones, instala dependencias npm y compila assets.

### Instalación manual

```bash
# 1. Clonar y acceder al directorio
git clone <repo-url>
cd sistema-torneos

# 2. Instalar dependencias PHP
composer install

# 3. Configurar entorno
cp .env.example .env
php artisan key:generate

# 4. Crear base de datos SQLite y migrar
touch database/database.sqlite
php artisan migrate

# 5. Instalar dependencias JS y compilar
npm install
npm run build
```

### Base de datos de demostración

```bash
# Seed con roles, admin y datos demo completos
php artisan db:seed

# O seeders individualmente:
php artisan db:seed --class=RolesAndAdminSeeder    # roles, permisos y admin
php artisan db:seed --class=DemoTournamentSeeder   # torneo demo con partidos y resultados
```

**Credenciales del usuario admin creado por el seeder:**

- Email: `admin@chacomer.test`
- Password: `Admin12345!`

El `DemoTournamentSeeder` genera:

- **Torneo:** Copa Chacomer 2026
- **Disciplinas:** Fútbol y Pádel
- **Categorías:** Fútbol Masculino (6 equipos, liga), Fútbol Femenino (4 equipos, liga), Pádel Primera (8 parejas, knockout), Pádel Mixto (6 parejas, knockout)
- Fixture completo de liga para fútbol y primera ronda de knockout para pádel
- Algunos resultados cargados para visualizar standings

---

## Desarrollo Local

```bash
composer run dev
```

Levanta en paralelo: servidor PHP, queue worker, log viewer (Pail) y Vite (HMR).

```bash
# Ejecutar tests
composer run test
# o
php artisan test
```

---

## Variables de Entorno Relevantes

| Variable | Valor por defecto | Descripción |
| -------- | ----------------- | ----------- |
| `DB_CONNECTION` | `sqlite` | Driver de base de datos |
| `SESSION_DRIVER` | `database` | Driver de sesiones |
| `QUEUE_CONNECTION` | `database` | Driver de colas |
| `CACHE_STORE` | `database` | Driver de caché |
| `APP_ENV` | `local` | Entorno de la app |

---

## Estructura de Vistas

```text
resources/views/
├── layouts/
│   ├── app.blade.php          # Layout autenticado
│   ├── dashboard.blade.php    # Layout dashboard
│   └── guest.blade.php        # Layout público
├── livewire/
│   ├── tournaments/           # Vistas de torneos
│   ├── disciplines/           # Vistas de disciplinas
│   ├── categories/            # Vistas de categorías
│   ├── players/               # Vistas de jugadores
│   ├── participants/          # Vistas de participantes
│   ├── matches/               # Calendario, scheduler, resultados
│   ├── standings/             # Tabla de posiciones
│   ├── brackets/              # Vista de llaves
│   ├── layout/navigation.blade.php
│   └── pages/auth/            # Login, register, reset password, etc.
└── components/                # Componentes reutilizables de UI
```
