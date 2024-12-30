# Krant
Article CMS for an amateur newspaper.
This project's front-end is in **dutch**. This repository accepts dutch besides english.

## Getting started

Configuration is done in `Util/Config.php`. Create this file by renaming `Util/Config.example.php`. If
necessary, the database connection can be configured here. The default configuration should work for the docker-compose
setup.

## Deployment (Apache)
Copy the `www/` folder to a webserver with PHP ≥ 5 and MySQL installed.

To set up the database, use `krant.sql` (this defines all the required tables).

## Deployment (Docker compose)
The repository is ready to deploy with docker compose (from the repository root). Just run:

```bash
docker-compose up
```

Depending on your installation of Docker Compose, use `docker compose` instead of `docker-compose`.

The deployment is now available at `http://localhost:80`. PHPMyAdmin is available at `http://localhost:8000`.

### Resetting the database
 - First stop all containers.
 - `docker rm krant-db-1 && docker volume rm krant_persistent`
 - Start the containers again.

## Tests

PHPStan tests:

```bash
php phpstan.phar
```

The analysis level is set in `phpstan.neon`, and should be increased over time.
