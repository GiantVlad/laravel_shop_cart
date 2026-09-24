# AGENTS.md — lara_shop

Laravel 12 + Vue 3 + Inertia + PrimeVue e-commerce app, served by RoadRunner/Laravel Octane, with queues and the
Horizon dashboard on Redis. Temporal is gone from the delivery path (sync controllers + Laravel Jobs); business logic
stays intact.

## Dev setup
- `docker-compose up` (may need 2-3 restarts on first boot).
- Create `.local_data/`. Copy `.env.example` → `.env`.
- `docker-compose exec roadrunner /bin/bash` → `composer install` → `php artisan migrate` → `php artisan db:seed --class=DatabaseSeeder`.
- JS: `cd frontend && npm install`.

## Build & test
- PHP static analysis: `./vendor/bin/phpstan analyse --memory-limit=2G --no-progress -c phpstan.neon` — no trailing path
  argument, it would override the config's `paths: app`. phpstan 2.2 + larastan 3.12 at level 7, with
  `phpstan-baseline.neon` freezing the existing 190 errors
  (`--generate-baseline=phpstan-baseline.neon` to accept more, e.g. after a toolchain jump).
- Backend tests: `docker compose exec -T roadrunner php artisan test`. They run against the dedicated `shop_test`
  database inside the dev MariaDB container, so `RefreshDatabase` no longer touches the `shop` you are working in (see the
  pitfalls).
- Frontend: `cd frontend && npm run build` (Vite → `public/build`, which is committed).
- There is **no JS test runner** in this repo; verify the UI in a browser (see the pitfalls).

## Conventions
- App modules: `app/*.php` (Admin, Catalog, Order, Product, etc.); controllers/services/repositories under `app/`.
- Tests: PHPUnit feature (`tests/Feature`), unit (`tests/Unit`).
- Temporal references in `app/` are out of the delivery path; the replacement is sync controllers + Laravel Jobs on
  Horizon/Redis. Note `app/Jobs/CheckPaymentJob` and `SendOrderToWarehouseJob` are still stubs — their `handle()` bodies
  are empty, so enabling the queue worker changes nothing observable until that logic is restored.
- Admin panel (`/admin/*`): Inertia pages in `frontend/src/Pages/Admin`, one shared stylesheet
  `frontend/src/assets/admin.css` (PrimeFlex utilities + `admin-*` classes, **no Bootstrap**), tests in `tests/Feature/Admin`.

## Production deploy
Host NGINX terminates TLS and proxies to the containerised RoadRunner; the vhost lives in the repo at
`.docker/nginx/prod/shop.cloud-workflow.com.conf` (installs to `/etc/nginx/sites-available/`, upstream `127.0.0.1:8089`).
RoadRunner serves the app *and* the static files from `/app/public`, so there is nothing to serve from disk.

- Files: `compose.yaml` = production (roadrunner + horizon + mariadb + redis), `docker-compose.yml` = dev. Always deploy
  with the explicit file: `docker compose -f compose.yaml ...`.
- Env file: services read `${ENV_FILE:-.env.prod}`. On the server the file is named `.env`, so it has to contain
  `ENV_FILE=.env` and `COMPOSE_FILE=compose.yaml`; a file named `.env` is then also what compose uses for `${DB_*}`
  interpolation, so no `--env-file` flag is needed. Keep it named `.env.prod` and you must pass `--env-file .env.prod`
  instead.
- Deploy:
  1. `docker compose -f compose.yaml up -d --build`
  2. `docker compose -f compose.yaml run --rm roadrunner php artisan migrate --force` (adds `failed_jobs` etc. — never automatic)
  3. `docker compose -f compose.yaml up -d horizon`
  4. After every later code deploy: `docker compose -f compose.yaml exec roadrunner rr -c /etc/.rr.yaml reset` and
     `docker compose -f compose.yaml exec horizon php artisan horizon:terminate`.
- One image (`lara_shop_app:prod`, built from `.docker/roadrunner/DockerfileProd`) backs both `roadrunner` and `horizon`;
  `.docker/roadrunner/config/rr.prod.yaml` is baked in at build time, so RR config changes require a rebuild.
- The base image is pinned by tag there and must exist on the registry: `.docker/roadrunner/docker-build.me` builds and
  pushes it (it must push the *pinned* tag, not just `:latest`).
- Redis databases are split per concern: 0 sessions, 1 cache, 2 queues + Horizon (`REDIS_CACHE_DB`/`REDIS_QUEUE_DB`), so
  `cache:clear` / `horizon:clear` cannot wipe each other or the sessions. Queue driver: `redis` in prod, `sync` in
  dev/tests.
- Horizon dashboard `/horizon` is open to any logged-in **admin** (gate in `App\Providers\HorizonServiceProvider`);
  restrict it via `HORIZON_ALLOWED_EMAILS`. Dev: `docker-compose exec roadrunner php artisan horizon` if you want the
  worker path locally.

## Pitfalls
- RoadRunner needs reset for code changes: `docker-compose exec roadrunner rr -c /etc/.rr.yaml reset`.
- XDebug + RoadRunner: set `pool.num_workers: 1`, `pool.debug: false`; disable active listener before reset.
- CI (`.github/workflows/ci.yml`), on push to `master`/`dev2` and PRs to `master`: PHP 8.4 + grpc; `phpunit.xml` pins
  `QUEUE_DRIVER=sync`, `CACHE_DRIVER`/`SESSION_DRIVER` `array` and `DB_DATABASE=shop_test` for the suite but deliberately
  *not* `DB_HOST` — CI supplies `127.0.0.1` for its mariadb service while the dev container supplies `mariadb`.
- `.env` and `.local_data/` are required; don't hand-edit generated `.rr.yaml` settings without checking docs.
- `php artisan test` runs against the dedicated `shop_test` database inside the dev MariaDB container, so
  `RefreshDatabase` leaves the `shop` database you are working in alone. `shop_test` is created by
  `.docker/mariadb/init/01-create-test-database.sql` on a **fresh** data directory (docker-entrypoint-initdb.d only runs
  then); on an existing installation create it by hand: `CREATE DATABASE shop_test; GRANT ALL ON shop_test.* TO
  'admin'@'%';`. The `admin` user only owns `shop` otherwise, and the suite reports "Access denied" rather than a hint.
  Dev-seeded reference numbers, for reseeding after manual damage: `products=199`, `catalogs=16`, `admins=1`.
- Test environment overrides in `phpunit.xml` must be `<server name="..." value="..." force="true"/>`, **not** `<env>`:
  Laravel's `env()` reads `$_SERVER` before `$_ENV`, and compose exports these variables into the container, so `<env>`
  entries were ignored entirely — the suite silently ran on the dev `shop` database with the dev redis cache and file
  sessions, and PHPUnit says nothing about it. Confirmed with a probe test inside the suite: `$_ENV='shop'`,
  `$_SERVER='shop_test'`.
- Both compose files live in the repo and compose prefers `compose.yaml`, so a bare `docker compose exec ...` picks the
  **production** file and dies on the missing prod variables. The dev `.env` (and the server's `.env`) therefore pin
  `COMPOSE_FILE` (`docker-compose.yml` / `compose.yaml`); without it dev commands need an explicit `-f docker-compose.yml`.
- RoadRunner workers cache routes *and* the Vite manifest: run `rr -c /etc/.rr.yaml reset` after changing routes or
  rebuilding assets, otherwise the browser keeps requesting the previous `assets/main-*.js`, which no longer exists →
  blank SPA on every page.
- Admin routes: `auth:admin` is applied on the route group in `routes/web.php`; the login/logout/password-reset routes sit
  **outside** that group. Admin feature tests need `$this->withoutMiddleware(VerifyCsrfToken::class)` because CSRF is
  enforced even in tests.
- Numeric property filters (`ProductRepository::buildFilteredQuery`) must compare with `CAST(property_values.value AS
  DOUBLE)`. A bare `CAST(... AS DECIMAL)` is `DECIMAL(10,0)` on MariaDB, so `"20.56"` rounds to 21 *before* the
  comparison: decimal values were dropped from their own range boundary and `99.5` passed a `>= 100` filter.
- `browser_use` is broken on this machine; verify UI changes with headless Chrome
  (`--headless=new --virtual-time-budget=9000 --screenshot=... --dump-dom`) or the CDP driver in the
  `lara-shop-feature-work` skill.

Production-specific traps (all hit and verified while setting up the prod stack):
- RoadRunner is PID 1 and spawns `server.command` from `rr.prod.yaml` as its worker, so it must be the Octane *worker*
  (`php /app/vendor/bin/roadrunner-worker`). `php artisan octane:start` there starts a nested RoadRunner of its own,
  prints its banner on STDOUT and RR aborts with "validation failed on the message sent to STDOUT" plus a bogus
  `rpc_plugin_serve: address -1919: invalid port`. The worker also needs `APP_BASE_PATH=/app` (compose sets it) or it
  dies with "Class Laravel\Octane\Octane not found".
- `App\Http\Middleware\TrustProxies` (in `app/Http/Kernel.php`) is what makes X-Forwarded-Proto/-For/-Host count behind
  the host NGINX. The framework middleware defaults to trusting nobody (`$proxies` is null) and this repo has no
  `config/trustedproxy.php`, so removing it silently turns every https URL into http and logs the proxy IP as the client.
- Laravel maps the env strings `null`/`true`/`false`/`empty` to PHP null/bool. `REDIS_PASSWORD=null` therefore means "no
  AUTH is sent": with a real password, redis' `--requirepass` must be fed that same value (compose.yaml wires both from
  one variable), and reverting the value to the literal `null` while requiring a password locks the app out of its cache
  and sessions.
- Horizon only loads the `environments` block matching `app()->environment()`. `APP_ENV=prod` here, so `config/horizon.php`
  declares both `production` and `prod`; if the block is missing the master starts anyway — "Horizon started successfully"
  — with zero supervisors and jobs pile up in redis unnoticed.
- Horizon runs in its own container (`php artisan horizon`, compose service `horizon`), not inside the RoadRunner one:
  `rr reset` restarts that whole process tree (would kill jobs mid-flight) and a runaway job must not be able to OOM the
  web server. `maxProcesses` in `config/horizon.php` is sized for that container's limits.
- mariadb runs the official image: it refuses to initialise without `MARIADB_ROOT_PASSWORD` ("Database is uninitialized and
  password option is not specified") and stores data in `/var/lib/mysql` (volume `db_data`). `MARIADB_SKIP_TEST_DB` was a
  bitnami-only variable and is gone.
- `env()` outside of config files returns null once `php artisan config:cache` has run on the server — keep settings read
  at runtime (e.g. the Horizon dashboard allowlist) in a config file.
- The prod image installs with `composer install --no-dev`, so nothing the *deploy* needs may use a dev dependency.
  `php artisan db:seed` died on the server with `Class "Faker\Factory" not found` because four seeders called
  `Faker::create()` while `fakerphp/faker` is require-dev: keep seeders on plain PHP (`mt_rand`, word lists).
- Toolchain: PHP 8.4, Laravel 12.69, PHPUnit 13.3, collision 8.9, larastan 3.12, phpstan 2.2, Octane 2.20. PHPUnit 13
  ignores doc-comment metadata, so data providers and every other annotation must be attributes
  (`#[DataProvider('weakPasswords')]`); a leftover `@dataProvider` passes zero arguments and fails the test rather than
  warning. PHPUnit 13 requires PHP >= 8.4.1 (`composer.json` says `php: ^8.4`, CI runs 8.4) and collision >= 8.9, which
  in turn needs `filp/whoops ^2.18.4` — that chain is what blocks PHPUnit 13 on older constraints, not PHPUnit itself.
- **Restart RoadRunner after *any* `composer update`**, not just for route/asset changes: the long-lived worker keeps the
  replaced classes in memory, so a vendor swap produces incoherent errors rather than "class not found".
  `/admin/login` threw `Inertia\Response::__construct(): Argument #3 ($props) must be of type array, string given` (a v3
  `Response` being called by a v2-era `ResponseFactory` still resident in the worker) and `rr -c /etc/.rr.yaml reset`
  fixed it. Same run needs `php artisan view:clear` when Blade directives changed.
- Inertia server and client must move together: `inertiajs/inertia-laravel` ^3 pairs with `@inertiajs/vue3` ^3. v3 moved
  the initial page JSON out of the `data-page` attribute into that element's text content
  (`<div data-page="app" type="application/json">{...}`), while the v2 client still does `JSON.parse(el.dataset.page)` —
  a v2 client with a v3 server parses `"app"` and the SPA never mounts, and nothing server-side reveals it.
- RoadRunner is two halves that must stay compatible: the `rr` binary (2025.1.0 in prod, 2025.1.15 in dev, copied from
  ghcr at image build) and the PHP packages (`spiral/roadrunner` 2025.1.x, `roadrunner-http` 4.x, `roadrunner-worker`,
  `spiral/goridge`). Changing only the PHP half changes the worker, so verify with `rr -c /etc/.rr.yaml reset` and then a
  `/health` + `/shop` fetch; `laravel/octane` must allow the pair (2.20 does, and it also raises the PHP-side floor).
- Composer cannot fetch the custom VCS package over ssh: the lock records
  `git@github.com:GiantVlad/braintreehttp_php.git` while the containers ship no ssh client, so *any* full re-resolution
  dies with `error: cannot run ssh`. Run
  `git config --global url."https://github.com/".insteadOf "git@github.com:"` inside the container first — it is not
  persisted, so either bake it into the dev image or repeat it after rebuilding the container.
- `/health` (`routes/web.php`) is what the image's `docker-healthcheck` and the compose healthcheck call; without a
  matching route the container reports unhealthy forever while still serving traffic.
