<?php

namespace UniWue\UwA11yCheck\CheckUrlGenerators;

use UniWue\UwA11yCheck\Utility\Exception\MissingConfigurationException;

/**
 * Class AbstractCheckUrlGenerator
 */
abstract class AbstractCheckUrlGenerator
{
    protected array $requiredConfiguration = [];
    protected string $tableName = '';
    protected string $editRecordTable = '';

    /**
     * AbstractCheckUrlGenerator constructor.
     */
    public function __construct(array $configuration)
    {
        $this->checkRequiredConfiguration($configuration);
    }

    public function getCheckUrl(int $pageUid): string
    {
        return '';
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getEditRecordTable(): string
    {
        return $this->editRecordTable;
    }

    /**
     * Checks, if all required configuration settings are available and if not, throws an exception
     */
    protected function checkRequiredConfiguration(array $configuration): void
    {
        foreach ($this->requiredConfiguration as $configurationKey) {
            if (!isset($configuration[$configurationKey])) {
                throw new MissingConfigurationException(
                    'Missing configuration key "' . $configurationKey . '" in ' . __CLASS__,
                    1573565583
                );
            }
        }
    }
}
