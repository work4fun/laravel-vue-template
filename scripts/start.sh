#!/bin/bash

SCRIPTS_DIR="$(dirname "$(dirname "${BASH_SOURCE[@]}")")"

cd "$SCRIPTS_DIR" || exit;

# shellcheck source=detect-docker.sh
source "$SCRIPTS_DIR/scripts/detect-docker.sh"

### login git hub docker repo
echo 7ce4f9e1fa44cc00339094d3a0d9b5adf01f9d78 | docker login docker.pkg.github.com -u HongjiangHuang --password-stdin

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

echo "http://127.0.0.1:80"
