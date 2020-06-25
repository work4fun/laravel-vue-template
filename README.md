## Devlopment Dependencies

- [docker](https://www.docker.com/)

### Start
```bash
bash ./scripts/start.sh
```

### Restart
```bash
docker-compose restart
```

### Develop
```bash
bash ./scripts/dev.sh
```

### Run commands
```bash
./artisan ${command}
```

### Run composer
```bash
docker-compose exec cli-app ./composer ${composer command}(update|install|require|...)
```

### Run nodejs commands
```bash
docker-compose exec nodejs yarn
```