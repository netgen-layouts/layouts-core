Netgen Block Manager
====================

# Installation instructions

[INSTALL.md](INSTALL.md)

# Running tests

Running tests requires that you have complete vendors installed, so run `composer install` before running the tests.

You can run unit tests by simply calling `vendor/bin/phpunit` from the repo root. This will use an in memory SQLite database.

You can also run unit tests on a real database. After you create the database, run the tests with:

```
$ DATABASE=mysql://root@localhost/ngbm vendor/bin/phpunit
```

where `mysql://root@localhost/ngbm` is a DSN to your MySQL database.

If you use PostgreSQL, you can use the following command:

```
$ DATABASE=pgsql://user:pass@localhost/ngbm vendor/bin/phpunit
```

# Running API tests

API tests are functional tests, meaning they need a fully functional Symfony app with Block Manager enabled.

To run the tests, you need to require some Composer packages:

```
composer require lakion/api-test-case:*
```

Afterwards, running tests is as simple as calling the following command:

```
DATABASE=mysql://root@localhost/bm_test vendor/bin/phpunit --bootstrap vendor/autoload.php -c vendor/netgen/block-manager/phpunit-api.xml
```

Notice that you need to specify the database the tests will use. This database needs to be the same as the database used by your Symfony app when in `test` environment.
