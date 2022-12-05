<?php

namespace Polygontech\DataMigration\Commands;

use Illuminate\Console\Command;
use Polygontech\DataMigration\Migrator;

class DataMigrateCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'data-migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run new migration files.';

    /**
     * Execute the console command.
     *
     * @param Migrator $migrator
     * @return void
     */
    public function handle(Migrator $migrator)
    {
        $migrator
            ->setOutput($this->output)
            ->run();
    }
}
