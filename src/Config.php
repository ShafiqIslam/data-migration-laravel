<?php

namespace Polygontech\DataMigration;

use Illuminate\Support\Str;

/**
 * @internal
 */
class Config
{
    private string $tableName;
    private string $namespace;
    private string $directory;

    public function __construct(string $tableName, string $namespace, string $directory)
    {
        $this->tableName = $tableName;
        $this->namespace = $namespace;
        $this->directory = $directory;
    }

    /**
     * Get the table name of the data migrations.
     *
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string  $name
     * @return string
     */
    public function makeFQCNFromfile($name): string
    {
        return $this->getNamespace() . "\\" . $this->makeClassNameFromFile($name);
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string  $name
     * @return string
     */
    public function makeClassNameFromFile($name): string
    {
        return $this->makeClassName(implode('_', array_slice(explode('_', $name), 4)));
    }

    /**
     * Get the class name of a migration name.
     *
     * @param  string  $name
     * @return string
     */
    public function makeClassName($name): string
    {
        return Str::studly($name);
    }

    /**
     * Get the directory name of the data migrations.
     *
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * Get the namespace of the data migrations.
     *
     * @return string
     */
    public function getNamespace(): string
    {
        return $this->namespace;
    }

    /**
     * Get the full path name to the migration.
     *
     * @param string $name
     * @return string
     */
    public function makeFilePath(string $name): string
    {
        return $this->directory . '/' . date('Y_m_d_His') . '_' . $name . '.php';
    }
}
