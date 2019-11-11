<?php
namespace UniWue\UwA11yCheck\CheckUrlGenerators;

use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

/**
 * Class AbstractCheckUrlGenerator
 */
abstract class AbstractCheckUrlGenerator
{
    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var UriBuilder
     */
    protected $uriBuilder = null;

    /**
     * @param UriBuilder $uriBuilder
     */
    public function injectUriBuilder(UriBuilder $uriBuilder)
    {
        $this->uriBuilder = $uriBuilder;
    }

    /**
     * AbstractCheckUrlGenerator constructor.
     * @param array $configuration
     */
    public function __construct(array $configuration)
    {
    }

    /**
     * @param string $baseUrl
     * @param int $pageUid
     */
    public function getCheckUrl(string $baseUrl, int $pageUid)
    {
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }
}
