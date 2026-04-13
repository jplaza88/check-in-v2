# Check-In v2

> ⚠️ **This project is currently in development and not ready for production.**
> The environment below is configured for local development via Laravel Sail (Docker).
> A separate production Docker environment will need to be configured before deploying.

A web application that manages truck drivers' check-in's across Martori Farms locations throughout the US.

## Stack

| Layer | Technology                       |
|---|----------------------------------|
| Backend | Laravel 13 |
| Language | PHP 8.5                          |
| Testing | Pest (Wrapper for PHPUnit)       |
| Database | PostgreSQL 18                    |
| Frontend | React · Inertia.js · Tailwind CSS |
| Cache / Queue | Redis                            |
| Mail | Mailpit                          |

## Laravel Packages & Tooling

| Tool | Purpose                                                    |
|---|------------------------------------------------------------|
| Sail | Docker-based local development environment                 |
| Octane | High-performance application server (FrankenPHP)           |
| Pint | PHP code style fixer (Wrapper built ontop of PHP-CS-Fixer) |
| Larastan | Static analysis (PHPStan for Laravel)                      |

## Requirements

- [Composer](https://getcomposer.org/)
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

```bash
# Install Composer (macOS)
brew install composer
```

## Installation

```bash
# 1. Clone the repository
git clone git@github.com:jplaza88/check-in-v2.git && cd check-in-v2

# 2. Install PHP dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Start Docker containers
./vendor/bin/sail up -d

# 5. Install and build frontend dependencies
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev   # or: npm run build

# 6. Run migrations and seeders
./vendor/bin/sail artisan migrate --seed
```

## Accessing the Application

| Service | URL |
|---|---|
| Web application | https://martori.vm |
| Mailpit inbox | http://localhost:8025 |

## Development

> 💡 Octane is configured to run in watch mode - backend changes are picked up automatically with no server restart needed.
```bash
# Frontend (watch mode)
./vendor/bin/sail npm run dev

# Linting
./vendor/bin/pint -v

# Static analysis
./vendor/bin/phpstan analyse --memory-limit=2G

# Tests
./vendor/bin/sail artisan test                                                             # via Artisan
./vendor/bin/sail pest                                                                     # via Pest directly
./vendor/bin/sail pest --filter "switches locale when locale is available"                 # run a specific test
./vendor/bin/sail pest tests/Unit/LocaleSwitchTest.php                                     # run a specific test
```
