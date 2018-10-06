<?php

namespace Vinorcola\ImportBundle\Config;

use LogicException;
use Vinorcola\ImportBundle\Model\ImportConsumerInterface;

class Config
{
    /**
     * @var string
     */
    private $temporaryDirectory;

    /**
     * @var array
     */
    private $importConfigs;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->temporaryDirectory = $config['temporaryDirectory'];
        $this->importConfigs = $config['imports'];
    }

    /**
     * @return string
     */
    public function getTemporaryDirectory(): string
    {
        return $this->temporaryDirectory;
    }

    /**
     * Get the list of all registered import.
     *
     * @return string[]
     */
    public function getImportNames(): array
    {
        return array_keys($this->importConfigs);
    }

    /**
     * Get the route name prefix for the given import.
     *
     * @param string $importName
     * @return string
     */
    public function getRouteNamePrefix(string $importName): string
    {
        $this->checkImportExists($importName);

        return $this->importConfigs[$importName]['route_prefix']['name'];
    }

    /**
     * Get the url prefix for the given import.
     *
     * @param string $importName
     * @return string
     */
    public function getUrlPrefix(string $importName): string
    {
        $this->checkImportExists($importName);

        return $this->importConfigs[$importName]['route_prefix']['url'];
    }

    /**
     * @param string $importName
     * @return string[]
     */
    public function getMapping(string $importName): array
    {
        $this->checkImportExists($importName);

        return $this->importConfigs[$importName]['mapping'];
    }

    /**
     * @param string $importName
     * @return ImportConsumerInterface
     */
    public function getConsumer(string $importName): ImportConsumerInterface
    {
        $this->checkImportExists($importName);

        return $this->importConfigs[$importName]['service'];
    }

    /**
     * @param string $importName
     */
    private function checkImportExists(string $importName): void
    {
        if (!key_exists($importName, $this->importConfigs)) {
            throw new LogicException('Import "' . $importName . '" doest not exists.');
        }
    }
}
