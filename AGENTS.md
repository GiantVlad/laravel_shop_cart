# Repository Guidelines

## Project Structure & Module Organization
This repository is a Laravel 12 backend with a Vue 3 + Inertia frontend.

- `app/`: core backend code (controllers, services, repositories, DTOs, Temporal workflows).
- `routes/`: HTTP/API route definitions (`web.php`, `api.php`).
- `resources/views/`: Blade templates (storefront and admin pages).
- `frontend/`: Vite-powered Vue application (`src/Pages`, `src/Layouts`, `main.js`).
- `database/migrations`, `database/seeders`, `database/factories`: schema and test/seed data.
- `tests/Unit`, `tests/Feature`: PHPUnit test suites.
- `public/`: built assets and static files.

## Build, Test, and Development Commands
Run from repository root unless noted.

- `docker-compose up`: start local stack (RoadRunner, DB, supporting services).
- `docker-compose exec roadrunner composer install`: install PHP dependencies in container.
- `docker-compose exec roadrunner php artisan migrate --seed`: apply migrations and seed data.
- `docker-compose exec roadrunner php artisan test`: run PHPUnit unit + feature tests.
- `docker-compose exec roadrunner vendor/bin/phpstan analyse`: static analysis (`level: 7`).
- `cd frontend && npm install`: install frontend dependencies.
- `cd frontend && npm run dev`: run Vite dev server.
- `cd frontend && npm run build`: produce production frontend build.

## Coding Style & Naming Conventions
- Follow PSR-12 for PHP (4-space indentation, one class per file, strict naming).
- Keep Laravel conventions: controllers in `app/Http/Controllers`, services in `app/Services`, repositories in `app/Repositories`.
- Use `PascalCase` for PHP classes, `camelCase` for methods/variables, and descriptive migration names.
- Vue SFC components under `frontend/src` use `PascalCase` filenames (for example `ProductList.vue`).

## Testing Guidelines
- PHPUnit is configured via `phpunit.xml`; test files must end with `*Test.php`.
- Put business-logic unit tests in `tests/Unit`; HTTP/integration coverage in `tests/Feature`.
- Prefer factories/seeders over hard-coded fixtures.
- Run tests before opening a PR: `docker-compose exec roadrunner php artisan test`.

## Commit & Pull Request Guidelines
Recent history shows short, imperative commit subjects (for example: `Upgrade php to 8.3`, `FE upgrade. Vue3 + Inertia`).

- Keep commit titles concise, action-first, and scoped to one change.
- Reference issue IDs in the PR description.
- PRs should include: purpose, risk/rollback notes, migration impact, and screenshots for UI changes.
- If dependencies are bumped, mention affected package names and versions.

## Security & Configuration Tips
- Do not commit secrets; keep `.env` local and update `.env.example` when adding new variables.
- Payment, Temporal, and external service keys must be provided via environment variables.
