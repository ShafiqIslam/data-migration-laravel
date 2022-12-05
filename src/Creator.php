<?php

namespace Polygontech\DataMigration;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use InvalidArgumentException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;

/**
 * @internal
 */
class Creator
{
    use ConsoleOutput;

    protected Filesystem $files;

    protected Config $config;

    protected Composer $composer;

    /**
     * DataMigration Creator constructor.
     *
     * @param Filesystem $files
     * @param Config $config
     * @param Composer  $composer
     */
    public function __construct(Filesystem $files, Config $config, Composer $composer)
    {
        $this->files = $files;
        $this->config = $config;
        $this->composer = $composer;
    }

    /**
     * Create a new migration at the given path.
     *
     * @param  string  $name
     * @param  string  $path
     * @throws Exception
     */
    public function create($name)
    {
        $file = $this->createFile($name);
        $this->autoload();
        $this->note("<info>Created Data Migration:</info> $file");
    }

    /**
     * Create a new migration at the given path.
     *
     * @param  string  $name
     * @param  string  $path
     * @throws Exception
     */
    public function createFile($name)
    {
        $this->ensureMigrationDoesNotExist($name);
        $this->ensureDirectory();
        $file_path = $this->config->makeFilePath($name);
        $this->files->put($file_path, $this->getBoilerplate($name));
        return pathinfo($file_path, PATHINFO_FILENAME);
    }

    /**
     * Ensure that a migration with the given name does not already exist.
     *
     * @param  string  $name
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    private function ensureMigrationDoesNotExist($name)
    {
        if (class_exists($className = $this->config->makeClassName($name))) {
            throw new InvalidArgumentException("A $className migration already exists.");
        }
    }

    private function ensureDirectory()
    {
        $directory = $this->config->getDirectory();
        $this->files->ensureDirectoryExists($directory);
    }

    private function autoload()
    {
        $this->setClassmap();
        $this->composer->dumpAutoloads();
    }

    private function setClassmap()
    {
        $composerJson = json_decode($this->files->get("composer.json"), 1);
        if ($this->hasClassmapInComposerJson($composerJson)) return;

        if (!array_key_exists("classmap", $composerJson['autoload'])) {
            $composerJson['autoload']['classmap'] = [];
        }
        $composerJson['autoload']['classmap'][] = $this->config->getDirectory();
        $this->files->put("composer.json", collect($composerJson)->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function hasClassmapInComposerJson(array $composerJson)
    {
        if (!array_key_exists("classmap", $composerJson['autoload'])) return false;

        $directory = $this->config->getDirectory();
        return in_array($directory, $composerJson['autoload']['classmap']);
    }

    /**
     * Make the migration boiler plate file.
     *
     * @param  string $name
     * @return string
     * @throws FileNotFoundException
     */
    private function getBoilerplate($name)
    {
        $content = $this->files->get($this->getBlankStub());
        return $this->populateBoilerplate($name, $content);
    }

    /**
     * Get the fqn to the stub.
     *
     * @return string
     */
    private function getBlankStub(): string
    {
        return $this->getBoilerplatePath() . '/blank.stub';
    }

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    private function getBoilerplatePath(): string
    {
        return __DIR__ . '/../boilerplate';
    }

    /**
     * Populate the place-holders in the migration stub.
     *
     * @param  string  $name
     * @param  string  $boilerplate
     * @return string
     */
    private function populateBoilerplate($name, $boilerplate): string
    {
        $boilerplate = str_replace('DummyClass', $this->config->makeClassName($name), $boilerplate);
        $boilerplate = str_replace('DummyNamespace', $this->config->getNamespace(), $boilerplate);
        return $boilerplate;
    }
}
