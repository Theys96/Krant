# Krant
Article CMS for an amateur newspaper.
This project's front-end is in **Dutch**. This repository accepts Dutch besides English.

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

## Deployment (Apache, FTP)

Make sure `lftp` is installed (`sudo apt-get install lftp`).

Create a file `.env` in the root of the project and fill in the FTP configuration:

```bash
FTP_USERNAME=
FTP_PASSWORD=
FTP_HOST=
FTP_PATH=
```

Create a copy of `www/app/Util/Config.example.php` in `Config.production.php` in the root of the project and fill in the production configuration.

Then, to deploy, run: 

```bash.
bin/install-prod
```

To set up the database, use `krant.sql` (this defines all the required tables).
