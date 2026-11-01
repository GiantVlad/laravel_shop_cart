#!/usr/bin/env bash

set -Eeuo pipefail

REPO_ROOT="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

if [[ $# -gt 1 ]]; then
    echo "Usage: $0 [env-file]" >&2
    exit 2
fi

ENV_FILE="${1:-}"
if [[ -z "$ENV_FILE" ]]; then
    if [[ -f .env ]]; then
        ENV_FILE=.env
    elif [[ -f .env.prod ]]; then
        ENV_FILE=.env.prod
    else
        echo "No .env or .env.prod found. Pass the production env file as an argument." >&2
        exit 1
    fi
fi

if [[ ! -f "$ENV_FILE" ]]; then
    echo "Environment file not found: $ENV_FILE" >&2
    exit 1
fi

COMPOSE=(docker compose --env-file "$ENV_FILE" -f compose.yaml)

mkdir -p storage/logs

echo "Building and starting the production application services..."
"${COMPOSE[@]}" up -d --build roadrunner horizon

echo "Clearing the shared Redis application cache..."
"${COMPOSE[@]}" exec -T roadrunner php artisan cache:clear

for SERVICE in roadrunner horizon; do
    echo "Clearing and rebuilding Laravel caches in ${SERVICE}..."
    # Redis cache was cleared once above; optimization files live in each container.
    "${COMPOSE[@]}" exec -T "$SERVICE" php artisan optimize:clear --except=cache
    "${COMPOSE[@]}" exec -T "$SERVICE" php artisan config:cache
    "${COMPOSE[@]}" exec -T "$SERVICE" php artisan view:cache
done

echo "Resetting RoadRunner workers..."
"${COMPOSE[@]}" exec -T roadrunner rr -c /etc/.rr.yaml reset

echo "Gracefully terminating Horizon workers..."
"${COMPOSE[@]}" exec -T horizon php artisan horizon:terminate

echo "Production application deploy completed. Migrations were not run."
