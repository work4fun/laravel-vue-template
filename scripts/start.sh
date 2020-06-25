#!/bin/bash

SCRIPTS_DIR="$(dirname "$(dirname "${BASH_SOURCE[@]}")")"

cd "$SCRIPTS_DIR" || exit;

# shellcheck source=detect-docker.sh
source "$SCRIPTS_DIR/scripts/detect-docker.sh"

mv "$SCRIPTS_DIR/laravel/.env.example" "$SCRIPTS_DIR/laravel/.env"

### start serice
docker-compose up -d

### init composer
docker-compose exec cli-app ./composer install --prefer-dist --no-dev --no-progress --optimize-autoloader

### database migrate
docker-compose exec cli-app ./artisan migrate

### key:generate
docker-compose exec cli-app ./artisan key:generate

### clear view cache
docker-compose exec cli-app ./artisan view:clear

### run nodejs yarn
docker-compose exec nodejs yarn

### build frontend resources
docker-compose exec nodejs yarn prod

### restart all application
docker-compose restart

echo "Start dashboard successful!"

for ip in $(ifconfig | sed -En 's/127.0.0.1//;s/.*inet (addr:)?(([0-9]*\.){3}[0-9]*).*/\2/p')
do
  echo "http://${ip}";
done