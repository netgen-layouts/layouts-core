Netgen Block Manager
====================

# Installation instructions

[INSTALL.md](INSTALL.md)

# Running tests

Running tests requires that you have complete vendors installed, so run
`composer install` before running the tests.

## Unit tests

Run the unit tests by calling `composer test` from the repo root:

```
$ composer test
```

This will use an in memory SQLite database.

You can also run unit tests on a real database. Create an empty MySQL
database and run the tests with:

```
$ DATABASE=mysql://root@localhost/ngbm composer test
```

where `mysql://root@localhost/ngbm` is a DSN to your MySQL database.

You can also use PostgreSQL:

```
$ DATABASE=pgsql://user:pass@localhost/ngbm composer test
```

## API tests

Run the API tests by calling `composer test-api` from the repo root:

```
$ composer test-api
```

Just as with unit tests, this will use a temporary SQLite database.

You can also use the `DATABASE` environment variable to run the tests
with a MySQL or PostgreSQL database:

```
$ DATABASE=mysql://root@localhost/ngbm composer test-api
```
