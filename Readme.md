<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

## This is a Laravel + Vue.js e-commerce template that will make it easy for you to start your own online store.

Stack: Laravel 12 + Vue 3 + Inertia + PrimeVue, served by RoadRunner (Laravel Octane) with MariaDB for storage.
Queues, retries and the Horizon dashboard run on Redis — the old Temporal workflows were replaced by sync controllers
and Laravel Jobs.

Production: <https://shop.cloud-workflow.com>

[Video presentation](https://youtu.be/McmVr2FEo-0)

<p><img src="https://preview.ibb.co/dyyGMb/sshot_shop.png" alt="sshot_shop" border="0"></p>

### Local setup
1. Setup docker and docker-compose on your local machine.
2. Create the `.local_data` folder in the root directory of the project.
3. Install git. Fetch this project from Github (git clone).
4. Copy `.env.example` and rename it to `.env`. Edit the file (database connection).
5. Run `docker compose up` (you may need to restart it 3-4 times on a first boot).
6. Front-end assets: `cd frontend && npm install` (`npm run build` writes `public/build`, which is committed).

Populate the database
```
docker compose exec roadrunner /bin/bash
composer install
php artisan migrate
php artisan db:seed --class=DatabaseSeeder
```

### Local endpoints
- `localhost` — main app
- `localhost:8025` — [Mailhog](https://github.com/mailhog/MailHog) (catches outgoing dev mail)
- `localhost:3307` — MariaDB, `localhost:6380` — Redis

### Tests
```
docker compose exec -T roadrunner php artisan test
```
They run against the `shop_test` database inside the same MariaDB container (second schema, created by
`.docker/mariadb/init/`), so `RefreshDatabase` never touches the dev `shop` database.

HTTPS locally
```
mkdir ssl && cd ssl && mkcert -install
```
(the certificate files in `ssl/` are gitignored)

### Queues and Horizon
Development runs `QUEUE_DRIVER=sync`, so jobs execute inline and no worker is needed. To exercise the worker path:
```
docker compose exec roadrunner php artisan horizon
```
The dashboard lives at `/horizon` and is only reachable by a logged-in admin (see
`App\Providers\HorizonServiceProvider`).

### RoadRunner
To reset the RoadRunner server execute
```
docker compose exec roadrunner rr -c /etc/.rr.yaml reset
```
To observe RoadRunner workers execute
```
docker compose exec roadrunner rr -c /etc/.rr.yaml workers -i
```
Reset after changing routes or rebuilding assets: the workers cache both the routes and the Vite manifest.

How to use [xdebug with roadrunner](https://roadrunner.dev/docs/php-debugging/2023.x/en)
. Set in the rr settings: pool.num_workers: 1, pool.debug: false.
If you have any active XDebug listener while starting RoadRunner with XDebug enabled — disable it. This will prevent false-positive debug session.
Start server, enable listener and run:
``docker compose exec roadrunner rr -c /etc/.rr.yaml reset http``

### Production
Docker Compose stack (`compose.yaml`): RoadRunner + Horizon + MariaDB + Redis, with nginx running on the host and
proxying TLS-terminated traffic to RoadRunner. The deploy steps, the server-side `.env` layout and the traps worth
knowing are documented in [AGENTS.md](AGENTS.md); the host nginx vhost is in
[.docker/nginx/prod/](.docker/nginx/prod/).

_Uladzimir Sadkou_: hofirma@gmail.com
