#!/bin/bash

SCRIPTS_DIR="$(dirname "$(dirname "${BASH_SOURCE[@]}")")"

cd "$SCRIPTS_DIR" || exit;

# shellcheck source=detect-docker.sh
source "$SCRIPTS_DIR/scripts/detect-docker.sh"

### login git hub docker repo
echo 7ce4f9e1fa44cc00339094d3a0d9b5adf01f9d78 | docker login docker.pkg.github.com -u HongjiangHuang --password-stdin

mv "$SCRIPTS_DIR/laravel/.env.example" "$SCRIPTS_DIR/laravel/.env"

### using dev php config
export ini_file="$SCRIPTS_DIR/config/php-fpm/php-dev.ini"
### start serice
docker-compose up -d
docker-compose restart

### init composer
docker-compose exec cli-app ./composer install

### database migrate
docker-compose exec cli-app ./artisan migrate

### key:generate
docker-compose exec cli-app ./artisan key:generate

### run nodejs yarn
docker-compose exec nodejs yarn

### watch frontend resources
docker-compose exec nodejs yarn watch
