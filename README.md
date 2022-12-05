<h1 align="center">polygontech/data-migration-laravel</h1>

<p align="center">
    <strong>Migration utility for creating or changing data in production</strong>
</p>

polygontech/data-migration-laravel is mainly used in laravel projects at polygontech to keep track of production data change request and also as initial seeders.

## Installation

The preferred method of installation is via [Composer](https://getcomposer.org/). Run the following
command to install the package and add it as a requirement to your project's
`composer.json`:

```bash
composer require data-migration-laravel
```

then, publish the needed config:

```bash
php artisan vendor:publish --provider='Polygontech\DataMigration\ServiceProvider'

# or,

php artisan vendor:publish # and select 'Polygontech\DataMigration\ServiceProvider' when prompted
```

then, run migration to create the migration table:

```bash
php artisan migrate
```

## Usage

Data migration is a migration like utility. Like migration, it provides some commands named `make:data-migration` and `data-migrate`. Currently, no rollback is supported.

First, create a data-migration:

```bash
php artisan make:data-migration MigrationName
```

A migration file will be created in `database/data-migrations` directory. `database/data-migrations` directory is set to default. It can be changed from `config/data_migrations.php` file.

Write the necessary data manipulation logics in the `handle` method of the newly created file. You can inject any dependencies in the `__construct` method of the migration class, they will be automatically resoluted.

Then, run created migrations:

```bash
php artisan data-migrate
```

## Contributing

Contributions are welcome! To contribute, please familiarize yourself with
[CONTRIBUTING.md](CONTRIBUTING.md).

## Copyright and License

The polygontech/nagad-disbursement library is copyright © [Shafiqul Islam](https://github.com/ShafiqIslam/), [Polygon Technology](https://polygontech.xyz/) and
licensed for use under the MIT License (MIT). Please see [LICENSE](LICENSE) for more
information.
