# Krant
Article CMS for an amateur newspaper.
This project's front-end is in **dutch**. This repository accepts dutch besides english.

## Deployment (Apache)
Copy the `www/` folder to a webserver with PHP ≥ 5 and MySQL installed.

Enter the required info, at least the MySQL credentials, in `serverconfig.php`. `config.php` generally does not need to be configured.

To set up the database, use `krant.sql` (this defines all the required tables).

## Deployment (Docker compose)
The repository is ready to deploy with docker compose (from the repository root).

### Resetting the database
 - First stop all containers.
 - `docker rm krant-db-1 && docker volume rm krant_persistent`
 - Start the containers again.
