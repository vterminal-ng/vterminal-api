# The VTerminal API

A Laravel based API that exposes endpoints of the vterminal application.

-   [Dependencies](#dependencies)
-   [Initial Setup](#initial-setup)
-   [Running Locally](#running-locally)
-   [Running database migrations](#running-database-migrations)
-   [Database Dashboard](#database-dashboard)
-   [Emails](#emails)
-   [Running Composer commands](#running-composer-commands)
<!-- -   [API Documentation](#api-documentation) -->

## Dependencies

Before you get started, you **MUST** have docker desktop installed

Download Docker Desktop [here](https://www.docker.com/products/docker-desktop).

## Initial Setup

Clone the project from github by running the following command

```
git clone https://github.com/vterminal-ng/vterminal-api.git
```

## Running Locally

From inside the `vterminal-api` folder run the following command.

To run the project in a docker environment, run the following command.

```
docker-compose up -d
```

The first time you run the above command it takes a few minutes, but subsequent runs are quick.

Install composer dependencies from the docker environment with the following command:

```
docker exec vterminal-php composer run project-setup-development
```

Once the application's Docker containers have been started, you can access the application in your web browser at: [http://localhost:2022](http://localhost:2022).

## Running database migrations

Before running the migrations, make sure you have started the docker container by running the command in the previous step.

To run the migrations, run the following command.

```
docker exec vterminal-php php artisan migrate
```

## Database Dashboard

View the database with phpmyadmin database dashboard at : [http://localhost:8083](http://localhost:8083)

`Username` - root_user

`Password` - password

## Emails

View test emails on mailhog here: [http://localhost:9025](http://localhost:9025).

## Running composer commands

To avoid dependency or versioning issues with the composer you have installed locally, we have added `composer` into the docker environment. To run composer in the container use the following

```
docker exec vterminal-php composer [COMMAND] [ARGS]
docker exec vterminal-php composer --version
```

<!-- ## API Documentation

View the API documentation at [http://localhost:2022/docs](http://localhost:2022/docs). -->
