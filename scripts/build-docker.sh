#!/bin/bash

SCRIPTS_DIR="$(dirname "$(dirname "${BASH_SOURCE[@]}")")"

cd "$SCRIPTS_DIR" || exit;

version=${1:-1.0}

#### build cli runtime
docker build . -f "./cli-dockerfile" -t docker.pkg.github.com/stoneycreeky/dashboard/dashboard-cli:"$version"

### build php-fpm runtime
docker build . -f "./fpm-dockerfile" -t  docker.pkg.github.com/stoneycreeky/dashboard/dashboard-fastcgi:"$version"

docker push docker.pkg.github.com/stoneycreeky/dashboard/dashboard-cli:"$version"
docker push docker.pkg.github.com/stoneycreeky/dashboard/dashboard-fastcgi:"$version"
