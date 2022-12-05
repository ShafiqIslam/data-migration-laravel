<?php

namespace Polygontech\DataMigration\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Composer;
use Polygontech\DataMigration\Creator;

class MakeDataMigrationCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:data-migration {name : The name of the migration.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new data migration file';

    /**
     * Execute the console command.
     *
     * @param  Creator  $creator
     * @return void
     * @throws \Exception
     */
    public function handle(Creator $creator)
    {
        $name = trim($this->input->getArgument('name'));
        $creator->setOutput($this->output)->create($name);
    }
}
