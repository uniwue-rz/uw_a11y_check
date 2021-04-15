<?php

namespace UniWue\UwA11yCheck\CheckUrlGenerators;

use UniWue\UwA11yCheck\Utility\Exception\MissingConfigurationException;

/**
 * Class AbstractCheckUrlGenerator
 */
abstract class AbstractCheckUrlGenerator
{
    /**
     * @var array
     */
    protected $requiredConfiguration = [];

    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var string
     */
    protected $editRecordTable = '';

    /**
     * AbstractCheckUrlGenerator constructor.
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
        $this->checkRequiredConfiguration($configuration);
    }

    /**
     * @param string $baseUrl
     * @param int $pageUid
     */
    public function getCheckUrl(string $baseUrl, int $pageUid): string
    {
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getEditRecordTable(): string
    {
        return $this->editRecordTable;
    }

    /**
     * Checks, if all required configuration settings are available and if not, throws an exception
     *
     * @param array $configuration
     */
    protected function checkRequiredConfiguration(array $configuration)
    {
        foreach ($this->requiredConfiguration as $configurationKey) {
            if (!isset($configuration[$configurationKey])) {
                throw new MissingConfigurationException(
                    'Missing configuration key "' . $configurationKey . '" in ' . __CLASS__,
                    1573565583355
                );
            }
        }
    }
}
