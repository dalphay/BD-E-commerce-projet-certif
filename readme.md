# Symfony docker skeleton

## Quick start
1. `composer install`
1. `docker-compose up`
1. go to http://localhost:8080

## Database settings

You can connect to MySQL with a client (like MySQL Workbench) with these settings :

- user : `root`
- password : `root`
- host : `127.0.0.1`
- port : `8083`

A default database called `db` is created on first mariadb container startup.

## Doctrine/Database related commands

To make these commands working, you have to run them INSIDE php-fpm container :

> `doctrine:database:create` which is **not needed**, as database already exists :
> ```shell
> docker-compose exec php-fpm sh -c "php bin/console doctrine:database:create"
> ```

> `make:migration` :
> ```shell
> docker-compose exec php-fpm sh -c "php bin/console make:migration"
> ```

> `doctrine:migrations:migrate` :
> ```shell
> docker-compose exec php-fpm sh -c "php bin/console doctrine:migrations:migrate"
> ```

> general purpose :
> ```shell
> docker-compose exec php-fpm sh -c "php bin/console <YOUR COMMAND GOES HERE>"
> ```