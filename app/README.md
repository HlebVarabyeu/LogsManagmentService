# Logs management service.

## Getting Started

Clone the repo locally:

```bash
git clone git@bitbucket.org:innowise-group/... test && cd test
```
## Prerequisites

Before you begin, ensure you have the following prerequisites:

- [Docker](https://www.docker.com/) - Make sure Docker is installed on your machine.
- [make](https://www.gnu.org/software/make/) - Ensure that the `make` utility is installed. It is commonly available on Unix-like systems.

## Installing

To install the project, run:

```bash
make install
```

## Running the Project

```bash
make start
```

## Stopping the Project

```bash
make stop
```

## Terminal inside php-cli container

```bash
make terminal
```

## Run php code style fixer and check

```bash
make cs-fixer
```

## Testing

You can use following GET request to count log entries

```
http://localhost/count?serviceNames[]=USER-SERVICE&serviceNames[]=INVOICE-SERVICE&startDate=2016-01-01T00:00:00Z&endDate=2023-12-31T23:59:59Z&statusCode=201
```

## How it works

There are two straightforward approaches to implement an infinite logs reading circle:
* Keep log's file opening all the time and read all incoming entries.
> Pros: it's easier to implement.

> Cons: it consumes more system resources, harder manage from the infrastructure perspective

* Read logs file at regular intervals
> Pros: less resource consumption, has trivial control via crontab,

> Cons: more complex implementation, need to control race condition

It seems like the second option is more preferable.

## Configuration inside .env file

* LOGS_FILE_PATH - path to source log file
* LOGS_FILE_PATH - number of log entries to read within command execution
* DATABASE_URL - DB credentials

## Architecture 

There are three layers here (hexagonal architecture in mind)
* Domain - it's empty here. No need to implement Log entry as Domain business entity in scope of the exercise. it'd be overkill a bit :)
* Application - layer to implement all user's cases. Scenarios of application use. Application services can be reused in any infrastructural contexts (controller, commands, workers etc)
* Infrastructure - layer where lives all platforms, frameworks, storages, transport channels and others implementations.

It's built with the idea of dependency inversion in mind.