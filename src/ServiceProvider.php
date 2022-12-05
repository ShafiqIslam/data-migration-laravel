<?php

namespace Polygontech\DataMigration;

use Polygontech\DataMigration\Contracts\RepositoryInterface;
use Polygontech\DataMigration\Repositories\Repository;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Polygontech\DataMigration\Commands\DataMigrateCommand;
use Polygontech\DataMigration\Commands\MakeDataMigrationCommand;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\ConnectionResolverInterface;

class ServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->runningInConsole()) return;

        $this->registerConfig();
        $this->registerRepository();
        $this->registerCommands();
    }

    /**
     * Register the migration configuration.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('data_migrations.php'),
        ]);

        $this->app->singleton(Config::class, function ($app) {
            $config = $app['config']['data_migrations'];
            return new Config(
                $config['table_name'],
                $config['namespace'],
                $config['directory']
            );
        });
    }

    /**
     * Register the migration repository service.
     *
     * @return void
     */
    protected function registerRepository()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');

        $this->app->singleton(RepositoryInterface::class, function ($app) {
            $resolver = $app->make(ConnectionResolverInterface::class);
            $config = $app->make(Config::class);
            return new Repository($resolver, $config->getTableName());
        });
    }

    /**
     * Register the data migration commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->commands(
            MakeDataMigrationCommand::class,
            DataMigrateCommand::class
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Config::class,
            RepositoryInterface::class,
            MakeDataMigrationCommand::class,
            DataMigrateCommand::class
        ];
    }
}
