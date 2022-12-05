<?php

namespace Polygontech\DataMigration\Contracts;

interface DataMigrationInterface
{
    /**
     * handle the migrations.
     *
     * @return void | string
     */
    public function handle();
}
