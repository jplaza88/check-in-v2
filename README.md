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

| Tool | Purpose                                             |
|---|-----------------------------------------------------|
| Sail | Docker-based local development environment          |
| Octane | High-performance application server (FrankenPHP)    |
| Pint | PHP code style fixer (Wrapper built ontop of PHP-CS-Fixer) |
| Larastan | Static analysis (PHPStan for Laravel)               |
| Rector | Automated code refactoring (Driftingly/Laravel Rector) |

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

# 4. Generate application key
./vendor/bin/sail artisan key:generate

# 5. Start Docker containers
./vendor/bin/sail up -d

# 6. Install and build frontend dependencies
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev   # or: npm run build

# 7. Run database migrations
./vendor/bin/sail artisan migrate

# 8. Run database seeders
./vendor/bin/sail artisan db:seed
```

## Accessing the Application

| Service | URL                       |
|---|---------------------------|
| Web application | https://martori.localhost |
| Mailpit inbox | http://localhost:8025     |

## Development

> Octane is configured to run in watch mode - backend changes are picked up automatically with no server restart needed.

### Frontend

```bash
./vendor/bin/sail npm run dev
```

### Code Style (Pint)

Uses extensive custom rules defined in `pint.json` - including `declare_strict_types`, `final_class`, `strict_comparison`, `ordered_class_elements`, and more. See `pint.json` for the full ruleset.

```bash
./vendor/bin/pint                    # fix all files
./vendor/bin/pint --dirty            # fix only uncommitted files
./vendor/bin/pint --test             # dry-run (report without fixing)
```

### Static Analysis (Larastan)

Runs at **level 8** (out of 10). Configuration in `phpstan.neon`.

```bash
./vendor/bin/phpstan analyse --memory-limit=2G
```

### Automated Refactoring (Rector)

Uses [Driftingly/Laravel Rector](https://github.com/driftingly/rector-laravel) with Laravel-specific rule sets and prepared sets for dead code, code quality, type declarations, privatization, and early returns. Configuration in `rector.php`.

```bash
vendor/bin/rector --dry-run          # preview changes without applying
vendor/bin/rector                    # apply changes
```

### Tests

```bash
./vendor/bin/sail artisan test                                              # all tests
./vendor/bin/sail artisan test --compact                                    # compact output
./vendor/bin/sail pest --filter "switches locale when locale is available"  # specific test
./vendor/bin/sail pest tests/Unit/LocaleSwitchTest.php                      # specific file
```
