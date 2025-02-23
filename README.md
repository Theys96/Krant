# Krant
Article CMS for an amateur newspaper.
This project's front-end is in **dutch**. This repository accepts dutch besides english.

## Getting started

Configuration is done in `Util/Config.php`. Create this file by renaming `Util/Config.example.php`. If
necessary, the database connection can be configured here. The default configuration should work for the docker-compose
setup.

## Install

Run:

```bash
bin/install
```

This downloads the dependencies and puts them in the right places.

## Run (Docker compose)
The project is ready to run with docker compose (from the repository root). Just run:

```bash
bin/start
```

The deployment is now available at `http://localhost:80`. PHPMyAdmin is available at `http://localhost:8000`.

### Resetting the database
 - First stop all containers.
 - `docker rm krant-db-1 && docker volume rm krant_persistent`
 - Start the containers again.

## Tests

PHPStan tests:

```bash
bin/phpstan
```

The analysis level is set in `www/phpstan.neon`, and should be increased over time.

Code style fixer:

```bash
bin/cs-fix
```

## Deployment (Apache)

First run:

```bash
bin/install-prod
```

Copy the contents of the `www/` folder to a webserver with PHP ≥ 8 and MySQL installed. Only the folders and the `.php`
files are required.

To set up the database, use `krant.sql` (this defines all the required tables).
