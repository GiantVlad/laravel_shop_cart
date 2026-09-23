# AGENTS.md — lara_shop

Laravel 12 + Vue 3 + Inertia + PrimeVue + RoadRunner e-commerce app (Temporal is being removed in favour of sync
controllers + Horizon/Redis; business logic stays intact).

## Dev setup
- `docker-compose up` (may need 2-3 restarts on first boot).
- Create `.local_data/`. Copy `.env.example` → `.env`.
- `docker-compose exec roadrunner /bin/bash` → `composer install` → `php artisan migrate` → `php artisan db:seed --class=DatabaseSeeder`.
- JS: `cd frontend && npm install`.

## Build & test
- PHP static analysis: `./vendor/bin/phpstan analyse --memory-limit=2G --no-progress -c phpstan.neon ./` (currently broken
  repo-wide: `phpstan.neon` uses PHPStan 1.x keys while `vendor/bin/phpstan` is 2.x — pre-existing, unrelated to feature work).
- Backend tests: `docker compose exec -T roadrunner php artisan test` (see the DB pitfall below).
- Frontend: `cd frontend && npm run build` (Vite → `public/build`, which is committed).
- There is **no JS test runner** in this repo; verify the UI in a browser (see the pitfalls).

## Conventions
- App modules: `app/*.php` (Admin, Catalog, Order, Product, etc.); controllers/services/repositories under `app/`.
- Tests: PHPUnit feature (`tests/Feature`), unit (`tests/Unit`).
- Temporal references in `app/` — being removed; keep business logic intact, replace delivery with sync controllers + Laravel Jobs (Horizon/Redis).
- Admin panel (`/admin/*`): Inertia pages in `frontend/src/Pages/Admin`, one shared stylesheet
  `frontend/src/assets/admin.css` (PrimeFlex utilities + `admin-*` classes, **no Bootstrap**), tests in `tests/Feature/Admin`.

## Pitfalls
- RoadRunner needs reset for code changes: `docker-compose exec roadrunner rr -c /etc/.rr.yaml reset`.
- XDebug + RoadRunner: set `pool.num_workers: 1`, `pool.debug: false`; disable active listener before reset.
- CI uses PHP 8.1 with grpc extension; `phpunit.xml` sets `QUEUE_DRIVER=sync` and `DB_HOST=mariadb_test`.
- `.env` and `.local_data/` are required; don't hand-edit generated `.rr.yaml` settings without checking docs.
- `php artisan test` runs against the **dev** database (the `mariadb_test` schema has no tables), so `RefreshDatabase`
  empties it: always re-seed afterwards (`php artisan db:seed --class=DatabaseSeeder` → `products=199`, `catalogs=16`,
  `admins=1`).
- RoadRunner workers cache routes *and* the Vite manifest: run `rr -c /etc/.rr.yaml reset` after changing routes or
  rebuilding assets, otherwise the browser keeps requesting the previous `assets/main-*.js`, which no longer exists →
  blank SPA on every page.
- Admin routes: `auth:admin` is applied on the route group in `routes/web.php`; the login/logout/password-reset routes sit
  **outside** that group. Admin feature tests need `$this->withoutMiddleware(VerifyCsrfToken::class)` because CSRF is
  enforced even in tests.
- `browser_use` is broken on this machine; verify UI changes with headless Chrome
  (`--headless=new --virtual-time-budget=9000 --screenshot=... --dump-dom`) or the CDP driver in the
  `lara-shop-feature-work` skill.
