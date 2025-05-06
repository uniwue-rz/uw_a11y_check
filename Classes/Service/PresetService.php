<?php

namespace UniWue\UwA11yCheck\Service;

use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use UniWue\UwA11yCheck\Analyzers\AbstractAnalyzer;
use UniWue\UwA11yCheck\Check\Preset;
use UniWue\UwA11yCheck\Check\TestSuite;
use UniWue\UwA11yCheck\CheckUrlGenerators\AbstractCheckUrlGenerator;
use UniWue\UwA11yCheck\Tests\TestInterface;
use UniWue\UwA11yCheck\Utility\Exception\ConfigurationFileNotFoundException;

/**
 * Class PresetService
 */
class PresetService
{
    protected YamlFileLoader $yamlFileLoader;
    protected FlashMessageService $flashMessageService;

    /**
     * PresetService constructor.
     */
    public function __construct()
    {
        $this->flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $this->yamlFileLoader = GeneralUtility::makeInstance(YamlFileLoader::class);
    }

    /**
     * Returns all presets
     */
    public function getPresets(SiteInterface $site): array
    {
        $yamlFile = $this->getConfigurationFile();
        $yamlData = $this->yamlFileLoader->load($yamlFile);
        $yamlData = $this->overrideFromSiteConfiguration($yamlData, $site);

        $presets = [];

        foreach ($yamlData['presets'] as $id => $presetData) {
            $name = $presetData['name'] ?? 'No name given';

            try {
                $analyzerConfig = $this->getConfiguration($presetData, 'analyzer', $yamlData);
                $analyzer = $this->getAnalyzerById($presetData['analyzer']['id'], $yamlData, $analyzerConfig);

                $checkUrlGeneratorConfig = $this->getConfiguration($presetData, 'checkUrlGenerator', $yamlData);
                $checkUrlGenerator = $this->getCheckUrlGeneratorById(
                    $presetData['checkUrlGenerator']['id'],
                    $yamlData,
                    $checkUrlGeneratorConfig
                );

                $testSuite = $this->getTestSuiteById(
                    $presetData['testSuite']['id'],
                    $yamlData,
                    $presetData['testSuite']
                );

                $configuration = $presetData['configuration'] ?? [];

                $presets[] = new Preset($id, $name, $analyzer, $checkUrlGenerator, $testSuite, $configuration);
            } catch (\Exception $exception) {
                $message = new FlashMessage(
                    $exception->getMessage(),
                    'Class not found in preset "' . $name . '"',
                    ContextualFeedbackSeverity::ERROR,
                    true
                );
                // @extensionScannerIgnoreLine False positive
                $this->flashMessageService->getMessageQueueByIdentifier(
                    'extbase.flashmessages.tx_uwa11ycheck_web_uwa11ychecktxuwa11ycheckm1'
                )->addMessage($message);
            }
        }

        return $presets;
    }

    /**
     * Returns a preset by the ID
     */
    public function getPresetById(string $id, SiteInterface $site): ?Preset
    {
        $result = null;

        $presets = $this->getPresets($site);
        foreach ($presets as $preset) {
            if ($preset->getId() === $id) {
                $result = $preset;
                break;
            }
        }
        return $result;
    }

    /**
     * Returns an analyzer by the ID
     */
    protected function getAnalyzerById(string $id, array $yamlData, array $configuration): ?AbstractAnalyzer
    {
        $analyzer = null;
        foreach ($yamlData['analyzers'] as $analyzerId => $analyzerConfig) {
            if ($analyzerId === $id) {
                $analyzer = GeneralUtility::makeInstance($analyzerConfig['className'], $configuration);
                break;
            }
        }

        // @todo: if no analyzer, throw an exception

        return $analyzer;
    }

    /**
     * Returns a CheckUrlGenerator by ID
     */
    protected function getCheckUrlGeneratorById(
        string $id,
        array $yamlData,
        array $configuration
    ): ?AbstractCheckUrlGenerator {
        $checkUrlGenerator = null;
        foreach ($yamlData['checkUrlGenerators'] as $checkUrlGeneratorId => $checkUrlGeneratorConfig) {
            if ($checkUrlGeneratorId === $id) {
                $checkUrlGenerator = GeneralUtility::makeInstance(
                    $checkUrlGeneratorConfig['className'],
                    $configuration
                );
                break;
            }
        }

        // @todo: if no analyzer, throw an exception

        return $checkUrlGenerator;
    }

    /**
     * Returns a testsuite by the ID
     */
    protected function getTestSuiteById(string $id, array $yamlData, array $configuration): TestSuite
    {
        $testSuite = GeneralUtility::makeInstance(TestSuite::class);
        foreach ($yamlData['testSuites'] as $testSuiteId => $testSuiteTests) {
            if ($testSuiteId === $id) {
                foreach ($testSuiteTests['tests'] as $testId => $test) {
                    $globalConfiguration = $test['configuration'] ?? [];
                    $localConfiguration = $configuration['tests'][$testId]['configuration'] ?? [];
                    ArrayUtility::mergeRecursiveWithOverrule($globalConfiguration, $localConfiguration);

                    /** @var TestInterface $test */
                    $test = GeneralUtility::makeInstance($test['className'], $globalConfiguration);
                    $testSuite->addTest($test);
                }
                break;
            }
        }

        return $testSuite;
    }

    /**
     * Merges local and global configuration and returns the result for the given type
     */
    protected function getConfiguration(array $presetData, string $type, array $yamlData): array
    {
        $globalConfiguration = $yamlData[$type . 's'][$presetData[$type]['id']]['configuration'] ?? [];
        $localConfiguration = $presetData[$type]['configuration'] ?? [];
        ArrayUtility::mergeRecursiveWithOverrule($globalConfiguration, $localConfiguration);

        return $globalConfiguration;
    }

    /**
     * Return the configuration file
     *
     * @return mixed
     */
    protected function getConfigurationFile()
    {
        $file = $GLOBALS['TYPO3_CONF_VARS']['UwA11yCheck']['Configuration'];
        if (!file_exists(GeneralUtility::getFileAbsFileName($file))) {
            throw new ConfigurationFileNotFoundException(
                'Configured yaml "' . $file . '" configuration does not exist.',
                1573216092
            );
        }

        return $file;
    }

    /**
     * Overrides the given YAML data with the site configuration.
     */
    private function overrideFromSiteConfiguration($yamlData, SiteInterface $site): array
    {
        if (!$site instanceof Site) {
            return $yamlData;
        }

        $siteConfiguration = $site->getConfiguration()['settings']['uw_a11y_check'] ?? [];
        ArrayUtility::mergeRecursiveWithOverrule($yamlData, $siteConfiguration);

        return $yamlData;
    }
}
