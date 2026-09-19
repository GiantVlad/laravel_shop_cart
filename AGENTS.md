# AGENTS.md — lara_shop

Laravel 10 + Vue 2 + Inertia + RoadRunner + Temporal e-commerce template (switching to sync controllers + Horizon/Redis, keeping business logic).

## Dev setup
- `docker-compose up` (may need 2-3 restarts on first boot).
- Create `.local_data/`. Copy `.env.example` → `.env`.
- `docker-compose exec roadrunner /bin/bash` → `composer install` → `php artisan migrate` → `php artisan db:seed --class=DatabaseSeeder`.
- JS: `npm install`.

## Build & test
- PHP static analysis: `./vendor/bin/phpstan analyse --memory-limit=2G --no-progress -c phpstan.neon ./`
- Backend tests: `php artisan test` (uses sqlite; see `.env.testing` for mariadb_test).
- Frontend tests: `npm run test` (mocha via mochapack; `tests/js/**/*.spec.js`).
- JS build: `npm run dev` / `watch` / `prod` (laravel-mix, vue-loader).

## Conventions
- App modules: `app/*.php` (Admin, Catalog, Order, Product, Temporal, etc.); controllers/services/repositories under `app/`.
- Tests: PHPUnit feature (`tests/Feature`), unit (`tests/Unit`), JS (`tests/js`).
- Temporal references in `app/` — being removed; keep business logic intact, replace delivery with sync controllers + Laravel Jobs (Horizon/Redis).

## Pitfalls
- RoadRunner needs reset for code changes: `docker-compose exec roadrunner rr -c /etc/.rr.yaml reset`.
- XDebug + RoadRunner: set `pool.num_workers: 1`, `pool.debug: false`; disable active listener before reset.
- CI uses PHP 8.1 with grpc extension; `phpunit.xml` sets `QUEUE_DRIVER=sync` and `DB_HOST=mariadb_test`.
- `.env` and `.local_data/` are required; don't hand-edit generated `.rr.yaml` settings without checking docs.
