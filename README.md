# Check-In

A web application that manages truck drivers' check-in's across Martori Farms locations throughout the US.

## Stack

| Layer | Technology                                         |
|---|----------------------------------------------------|
| Backend | Laravel 13 · Octane (FrankenPHP) · Pint · Larastan |
| Language | PHP 8.5                                            |
| Testing | Pest                                               |
| Database | PostgreSQL 18                                      |
| Frontend | React · Inertia.js · Tailwind CSS                  |
| Cache / Queue | Redis                                              |
| Mail | Mailpit                                            |
| Environment | Laravel Sail (Docker)                              |

## Requirements

- [Composer](https://getcomposer.org/)
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

## Installation

```bash
# 1. Clone the repository
git clone git@github.com:jplaza88/check-in-v2.git && cd check-in-v2

# 2. Install PHP dependencies
composer install

# 3. Copy environment file and generate app key
cp .env.example .env

# 4. Start Docker containers
./vendor/bin/sail up -d

# 5. Install and build frontend dependencies
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev   # or: npm run build for production

# 6. Run migrations and seeders
./vendor/bin/sail artisan migrate --seed
```

## Development

```bash
# Frontend (watch mode)
./vendor/bin/sail npm run dev

# Linting
./vendor/bin/pint -v

# Static analysis
./vendor/bin/phpstan analyse --memory-limit=2G

# Tests
./vendor/bin/sail artisan test
```
