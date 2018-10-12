Netgen Layouts
==============

This repository is the core/kernel of Netgen Layouts. It is not to be installed
as a standalone package. Instead, read the [installation instructions](https://docs.netgen.io/projects/layouts/en/latest/reference/install_instructions.html)
on how to install the complete Netgen Layouts to your Symfony based app.

# For developers

If you intend to develop Netgen Layouts, fix a bug, send a pull request and so
on, please read the following sections on how to run various test suites.

Running tests requires that you have complete vendors installed, so run
`composer install` before running the tests.

## Unit tests

Run the unit tests by calling `composer test` from the repo root:

```
$ composer test
```

This will use an in memory SQLite database.

You can also run unit tests on a real database. Create an empty MySQL database
and run the tests with:

```
$ DATABASE=mysql://root@localhost/nglayouts composer test
```

where `mysql://root@localhost/nglayouts` is a DSN to your MySQL database.

You can also use PostgreSQL:

```
$ DATABASE=pgsql://user:pass@localhost/nglayouts composer test
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
$ DATABASE=mysql://root@localhost/nglayouts composer test-api
```

## Behat tests

Some parts of the administration interface are covered with Behat tests. These
tests use Chrome WebDriver to run. Before running tests, you need to install
the Chrome WebDriver and run it, together with the Symfony web server used for
testing. There is a convenient shell script `tests/prepare_behat.sh`, which
will download the latest Chrome WebDriver to `vendor/bin/chromedriver`, run it
and start the test web server.

To run the tests, just execute the following:

```
$ composer behat
```

This will run the tests with the Chrome UI visible.

To run the tests without the Chrome UI and save some seconds, you can use:

```
$ chrome behat-headless
```

## PHPStan static analysis

All code is statically analysed with PHPStan. Make sure that PHPStan is green
for the entire codebase after your changes. Run the following two commands to
run PHPStan for the library/bundle code and for tests code, respectivelly:

```
$ composer phpstan
```

```
$ composer phpstan-tests
```

## Coding standards

This repo uses PHP CS Fixer and rules defined in `.php_cs` file to enforce coding
standards. Please check the code for any CS violations before submitting patches:

```
$ php-cs-fixer fix
```
