#!/usr/bin/env bash

# Source the ".env" file so Laravel's environment variables are available...
if [ -n "$APP_ENV" ] && [ -f ./.env."$APP_ENV" ]; then
  source ./.env."$APP_ENV";
elif [ -f ./.env ]; then
  source ./.env;
fi

# Define environment variables...
export WWWUSER=${WWWUSER:-$UID}
export WWWGROUP=${WWWGROUP:-$(id -g)}

ARGS=("$@")

# Run Docker Compose with the defined arguments...
docker compose "${ARGS[@]}"

