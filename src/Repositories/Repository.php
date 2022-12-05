<?php

namespace Polygontech\DataMigration\Repositories;

use Polygontech\DataMigration\Contracts\RepositoryInterface;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;

/**
 * @internal
 */
class Repository extends DatabaseMigrationRepository implements RepositoryInterface
{
}
