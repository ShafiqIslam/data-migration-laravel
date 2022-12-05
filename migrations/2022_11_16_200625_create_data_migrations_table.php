<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Polygontech\DataMigration\Config;

class CreateDataMigrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var Config */
        $config = app(Config::class);

        Schema::create($config->getTableName(), function (Blueprint $table) {
            $table->increments('id');
            $table->string('migration');
            $table->integer('batch')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /** @var Config */
        $config = app(Config::class);

        Schema::dropIfExists($config->getTableName());
    }
};
