Netgen Block Manager
====================

# Installation instructions

[INSTALL.md](INSTALL.md)

# Running tests

You can run unit tests by simply calling `phpunit` from the repo root. This will use an in memory SQLite database.

You can also run unit tests on a real database. After you create the database, run the tests with:

```
$ DATABASE=mysql://root@localhost/ngbm phpunit
```

where `mysql://root@localhost/ngbm` is a DSN to your database.
