# About IhandCashier Project
#### Requirement
- NativePHP v2+
- PHP 8.4
- NodeJS 22+
- [Laravel 12](https://laravel.com/docs/12.x/)
- [Native PHP v1+](https://nativephp.com/docs/desktop/1/getting-started/introduction)

# Instructions for IhandCashier Project
See example app on https://ihandcashier.basapadi.com with latest development
## Project Overview
- This is a Laravel 12 + NativePHP application for web and desktop cashier management.
- The codebase is organized by Laravel conventions, with customizations for NativePHP desktop integration.
- Main app logic is in `app/` (Controllers, Models, Helpers, Transformers, Providers).
- Configuration is in `config/` and `.env` (copied from `.env.example`).
- Database migrations, factories, and seeders are in `database/`.

## Developer Workflows
- Install dependencies: `composer install` and `npm install`.
- Copy `.env.example` to `.env` and adjust settings.
- Generate app key: `php artisan key:generate`.
- Migrate and seed database: `php artisan migrate` and `php artisan db:seed`.
- For NativePHP desktop: `php artisan native:migrate` and `php artisan native:seed`.
- Start development servers:
  - Web: `php artisan native:run` (backend) and `npm run dev` (frontend)
  - Or both: `composer native:dev`
- Build desktop app: `php artisan native:build <os>` (e.g., `win`, `mac`)

## Key Architectural Patterns
- Models in `app/Models/` extend `BaseModel` or `BaseUser` for shared logic.
- Controllers in `app/Http/Controllers/` handle HTTP and desktop requests.
- Service Providers in `app/Providers/` register app services and NativePHP integration.
- Helpers in `app/Helpers/` provide utility functions (e.g., `ConfigHelper.php`).
- Transformers in `app/Transformers/` handle data formatting for APIs/UI.
- Configuration is modularized in `config/` (e.g., `config/btx.php`, `config/nativephp.php`).

## Project-Specific Conventions
- Uses SQLite for local development (see `database/nativephp.sqlite`).
- NativePHP commands (`php artisan native:*`) are used for desktop features.
- Custom config files (e.g., `btx.php`, `ihandcashier.php`) define app-specific settings.
- Asset pipeline uses Vite (`vite.config.js`) and Tailwind (`tailwind.config.js`).

## Integration Points
- NativePHP for desktop integration (see `config/nativephp.php`, `app/Providers/NativeAppServiceProvider.php`).
- Laravel Fractal for data transformation (`config/fractal.php`, `app/Transformers/`).
- External packages managed via Composer and NPM (see `composer.json`, `package.json`).

## Examples
- To add a new model: create in `app/Models/`, extend `BaseModel`, add migration in `database/migrations/`.
- To add a config: create a new file in `config/` and reference via `config('yourfile.key')`.
- To add a desktop feature: use NativePHP commands and update `app/Providers/NativeAppServiceProvider.php`.

Refer to the README.md and config files for more details on setup and conventions.
