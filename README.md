Netgen Block Manager
====================

# Installation instructions

[INSTALL.md](INSTALL.md)

# Running tests

Running unit tests requires an empty database to be created. After you create the database, run the tests with:

```
$ DATABASE=mysql://root@localhost/ngbm phpunit
```

where `mysql://root@localhost/ngbm` is a DSN to your database.
