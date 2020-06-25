#!/bin/bash
if ! [ -x "$(command -v docker)" ]; then
  # auto install docker
  curl -sSL https://get.docker.com | sh
fi

if ! [ -x "$(command -v docker-compose)" ]; then
  # auto install docker-compose
  curl -L "https://github.com/docker/compose/releases/download/1.26.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
  chmod +x /usr/local/bin/docker-compose
fi
