Netgen Block Manager
====================

# Installation instructions

[INSTALL.md](INSTALL.md)

# Running tests

Running tests requires that you have complete vendors installed, so run
`composer install` before running the tests.

You can run unit tests by simply calling `composer test` from the repo root.
This will use an in memory SQLite database.

You can also run unit tests on a real database. After you create the database,
run the tests with:

```
$ DATABASE=mysql://root@localhost/ngbm composer test
```

where `mysql://root@localhost/ngbm` is a DSN to your MySQL database.

If you use PostgreSQL, you can use the following command:

```
$ DATABASE=pgsql://user:pass@localhost/ngbm composer test
```

# Running API tests

To run the API tests, you need a database. Create an empty MySQL database and
run the tests with:

```
$ DATABASE=mysql://user:pass@localhost/ngbm composer test-api
```
