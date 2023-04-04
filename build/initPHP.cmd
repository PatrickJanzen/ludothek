@echo off
docker compose exec php composer install
docker compose exec php bin/console d:s:u --force
docker compose exec php bin/console d:f:l -n
