<?php

namespace Polygontech\DataMigration;

use Polygontech\DataMigration\Contracts\RepositoryInterface;
use Polygontech\DataMigration\Contracts\DataMigrationInterface;
use Illuminate\Filesystem\Filesystem;

class Migrator
{
    use ConsoleOutput;

    /**
     * The migration repository implementation.
     */
    protected RepositoryInterface $repo;

    /**
     * The filesystem instance.
     */
    protected Filesystem $files;

    /**
     * The configuration set by the user.
     */
    protected Config $config;

    /**
     * Create a new migrator instance.
     *
     * @param  RepositoryInterface $repo
     * @param  Filesystem $files
     */
    public function __construct(RepositoryInterface $repo, Filesystem $files, Config $config)
    {
        $this->repo = $repo;
        $this->files = $files;
        $this->config = $config;
    }

    public function run()
    {
        $this->note('<info>Looking for data migrations.</info>');

        $files = $this->getMigrationFiles();
        $ran = $this->repo->getRan();
        $migrations = array_diff($files, $ran);

        if (count($migrations) == 0) {
            $this->note('<info>Nothing to migrate.</info>');
            return;
        }

        $this->runMigrationFiles($migrations);
    }

    /**
     * Run "up" a migration instance.
     *
     * @param  array  $migrations
     * @return void
     */
    protected function runMigrationFiles($migrations)
    {
        $this->note('<info>Running data migrations.</info>');
        $batch = $this->repo->getNextBatchNumber();
        foreach ($migrations as $file) {
            $this->handleMigration($file, $batch);
        }
        $this->note('<info>Data Migration Done.<info>');
    }

    /**
     * Run "handle" a migration instance.
     *
     * @param  string  $file
     * @param  int     $batch
     * @return void
     */
    protected function handleMigration($file, $batch)
    {
        $migration = $this->resolve($file);
        if (is_null($migration)) return;

        $result = $migration->handle();
        $this->repo->log($file, $batch);
        $this->note("<info>Migrated:</info> $file");
        if ($result) $this->note("<info>Returned:</info> $result");
    }

    /**
     * Get all of the migration files in configured path.
     *
     * @return array
     */
    public function getMigrationFiles()
    {
        return $this->getMigrationFilesInPath($this->config->getDirectory());
    }

    /**
     * Get all of the migration files in a given path.
     *
     * @param  string  $path
     * @return array
     */
    public function getMigrationFilesInPath($path)
    {
        $files = $this->files->glob($path . '/*_*.php');
        if ($files === false) return [];
        return $this->formatMigrationFiles($files);
    }

    private function formatMigrationFiles($files)
    {
        $files = array_map(function ($file) {
            return str_replace('.php', '', basename($file));
        }, $files);
        sort($files);
        return $files;
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string  $file
     * @return DataMigrationInterface | null
     */
    public function resolve($file)
    {
        $class = $this->config->makeFQCNFromfile($file);
        $object = app($class);
        if ($object instanceof DataMigrationInterface) return $object;

        $this->note("<info>$file does not implement DataMigrationInterface. Skipping.</info>");
        return null;
    }
}
